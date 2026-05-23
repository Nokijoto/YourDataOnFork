<?php

namespace App\Http\Controllers;

use App\Models\CapturedRequest;
use App\Models\CapturedPacket;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class WebhookController extends Controller
{
    /**
     * CORS headers dołączane do każdej odpowiedzi z tego kontrolera.
     * Phishingowe strony mogą być na innych domenach.
     */
    private function corsHeaders(): array
    {
        return [
            'Access-Control-Allow-Origin'  => '*',
            'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, X-Source, Authorization',
        ];
    }

    /**
     * Obsługa preflight OPTIONS (CORS).
     */
    public function options(): Response
    {
        return response('', 204)->withHeaders($this->corsHeaders());
    }

    /**
     * POST /api/webhook/capture
     * Odbiera dane z phishingowych stron (formularze, keylogger, fingerprint).
     */
    public function capture(Request $request): \Illuminate\Http\JsonResponse
    {
        // Zbierz wszystkie nagłówki HTTP, ale usuń sekrety jeśli przyszły.
        $headers = collect($request->headers->all())
            ->except(['authorization', 'cookie'])
            ->toArray();

        // Geolokalizacja – prosta, bez zewnętrznego API (można rozbudować)
        $geo = null;
        $ip = $request->ip();

        // Wyciągnij dane z body (JSON lub form-data)
        $body = $request->all();
        $source = $request->header('X-Source')
            ?? $request->input('source', 'unknown');
        $payload = $request->input('payload', $body);
        if (isset($payload['source'])) {
            unset($payload['source']);
        }

        $cookieMetadata = $request->input('cookie_metadata')
            ?? data_get($payload, '_cookie_metadata')
            ?? $this->cookieMetadataFromBody($body)
            ?? $this->cookieMetadataFromRequest($request);

        $record = CapturedRequest::create([
            'source'         => strtolower($source),
            'ip_address'     => $ip,
            'user_agent'     => $request->userAgent(),
            'referer'        => $request->header('Referer') ?? $request->input('referrer'),
            'headers'        => $headers,
            'cookie_metadata' => $cookieMetadata,
            'payload'        => $payload,
            'request_body'   => $this->redactSensitiveData($body),
            'geo'            => $geo,
            'request_method' => $request->method(),
            'request_url'    => $request->input('url'),
        ]);

        // Zapisz do cache żeby SSE stream mógł to odebrać
        $this->pushLiveEvent('capture', [
            'id'         => $record->id,
            'source'     => $record->source,
            'ip'         => $record->ip_address,
            'ua'         => mb_strimwidth($record->user_agent ?? '', 0, 60, '…'),
            'payload'    => $record->payload_preview,
            'created_at' => $record->created_at->format('H:i:s'),
        ]);

        return response()->json(['status' => 'captured', 'id' => $record->id])
            ->withHeaders($this->corsHeaders());
    }

    /**
     * POST /api/webhook/fingerprint
     * Odbiera pełny fingerprint urządzenia/przeglądarki z agenta demo.
     */
    public function fingerprint(Request $request): \Illuminate\Http\JsonResponse
    {
        $body = $request->all();
        $headers = collect($request->headers->all())
            ->except(['authorization', 'cookie'])
            ->toArray();

        $fingerprintId = $request->input('id');
        $ua = $request->input('ua') ?? $request->userAgent();
        $browserName = data_get($body, 'browser.name', 'unknown browser');
        $browserVersion = data_get($body, 'browser.version');
        $osName = data_get($body, 'os.name', 'unknown os');
        $osVersion = data_get($body, 'os.version');
        $deviceType = data_get($body, 'device.type', 'unknown device');

        $payload = [
            '_event' => 'fingerprint',
            '_fingerprint_id' => $fingerprintId,
            'summary' => [
                'device' => $deviceType,
                'os' => trim("{$osName} {$osVersion}"),
                'browser' => trim("{$browserName} {$browserVersion}"),
                'screen' => $request->input('screen'),
                'timezone' => $request->input('timezone'),
                'language' => $request->input('language'),
                'network' => $request->input('network'),
                'battery' => $request->input('battery'),
            ],
            'fingerprint' => $body,
        ];

        $recordData = [
            'source' => strtolower($request->header('X-Source') ?? $request->input('source', 'fingerprint')),
            'external_type' => 'fingerprint',
            'external_id' => $fingerprintId,
            'ip_address' => $request->ip(),
            'user_agent' => $ua,
            'referer' => $request->header('Referer') ?? $request->input('referrer'),
            'headers' => $headers,
            'cookie_metadata' => $this->cookieMetadataFromBody($body)
                ?? $this->cookieMetadataFromRequest($request),
            'payload' => $payload,
            'request_body' => $this->redactSensitiveData($body),
            'geo' => $request->input('geo'),
            'request_method' => $request->method(),
            'request_url' => $request->input('url') ?? $request->headers->get('origin'),
        ];

        $record = filled($fingerprintId)
            ? CapturedRequest::updateOrCreate(
                ['external_type' => 'fingerprint', 'external_id' => $fingerprintId],
                $recordData,
            )
            : CapturedRequest::create($recordData);

        $this->pushLiveEvent('capture', [
            'id' => $record->id,
            'source' => $record->source,
            'ip' => $record->ip_address,
            'ua' => mb_strimwidth($record->user_agent ?? '', 0, 60, '…'),
            'payload' => "fingerprint: {$deviceType} | {$browserName} | {$osName}",
            'created_at' => $record->created_at->format('H:i:s'),
        ]);

        return response()->json([
            'status' => $record->wasRecentlyCreated ? 'fingerprint_captured' : 'fingerprint_updated',
            'id' => $record->id,
            'fingerprint_id' => $fingerprintId,
        ])->withHeaders($this->corsHeaders());
    }

    /**
     * POST /api/webhook/packet
     * Odbiera dane z network sniffera (scapy/tshark na routerze Eryka).
     */
    public function packet(Request $request): \Illuminate\Http\JsonResponse
    {
        $record = CapturedPacket::create([
            'interface'       => $request->input('interface'),
            'protocol'        => strtoupper($request->input('protocol', 'OTHER')),
            'src_ip'          => $request->input('src_ip'),
            'dst_ip'          => $request->input('dst_ip'),
            'src_port'        => $request->input('src_port'),
            'dst_port'        => $request->input('dst_port'),
            'packet_size'     => $request->input('packet_size', 0),
            'ttl'             => $request->input('ttl'),
            'flags'           => $request->input('flags'),
            'payload_preview' => $request->input('payload_preview'),
            'summary'         => $request->input('summary'),
            'raw'             => $request->input('raw'),
        ]);

        // Tylko "ciekawe" pakiety trafiają do live stream (HTTP, DNS, ARP)
        $interestingProtocols = ['HTTP', 'HTTPS', 'DNS', 'ARP'];
        if (in_array($record->protocol, $interestingProtocols)) {
            $this->pushLiveEvent('packet', [
                'id'       => $record->id,
                'protocol' => $record->protocol,
                'src_ip'   => $record->src_ip,
                'dst_ip'   => $record->dst_ip,
                'src_port' => $record->src_port,
                'dst_port' => $record->dst_port,
                'summary'  => $record->summary,
                'size'     => $record->packet_size,
                'created_at' => $record->created_at->format('H:i:s'),
            ]);
        }

        return response()->json(['status' => 'stored', 'id' => $record->id])
            ->withHeaders($this->corsHeaders());
    }

    /**
     * GET /api/live-feed
     * Server-Sent Events – strumień na żywo do dashboardu operatora.
     * Używa cache (Redis/file) jako kolejki eventów między procesami.
     */
    public function stream(Request $request): Response
    {
        $headers = array_merge($this->corsHeaders(), [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no',    // Nginx: wyłącz buforowanie
            'Connection'        => 'keep-alive',
        ]);

        return response()->stream(function () {
            $lastId = 0;

            // Utrzymuj połączenie przez max 30 sekund (Apache/Nginx timeout)
            $maxTime = time() + 25;

            while (time() < $maxTime) {
                // Pobierz eventy z cache nowsze niż ostatnio wysłane
                $events = Cache::get('live_events', []);

                foreach ($events as $event) {
                    if ($event['seq'] > $lastId) {
                        $lastId = $event['seq'];
                        echo "id: {$event['seq']}\n";
                        echo "event: {$event['type']}\n";
                        echo "data: " . json_encode($event['data']) . "\n\n";
                        ob_flush();
                        flush();
                    }
                }

                // Keepalive co 5 sekund żeby połączenie nie padło
                echo ": keepalive\n\n";
                ob_flush();
                flush();

                sleep(2);
            }
        }, 200, $headers);
    }

    /**
     * GET /api/latest
     * Ostatnie N capture + packet (dla terminala, polling fallback).
     */
    public function latest(Request $request): \Illuminate\Http\JsonResponse
    {
        $captures = CapturedRequest::latest()->limit(10)->get([
            'id', 'source', 'ip_address', 'user_agent', 'payload', 'created_at'
        ]);

        $packets = CapturedPacket::latest()->limit(20)->get([
            'id', 'protocol', 'src_ip', 'dst_ip', 'src_port', 'dst_port', 'summary', 'packet_size', 'created_at'
        ]);

        return response()->json([
            'captures' => $captures,
            'packets'  => $packets,
        ])->withHeaders($this->corsHeaders());
    }

    /**
     * Wrzuca event do cache jako kolejka dla SSE stream.
     */
    private function pushLiveEvent(string $type, array $data): void
    {
        $events = Cache::get('live_events', []);

        // Sekwencer – monotoniczny licznik
        $seq = Cache::increment('live_events_seq');

        $events[] = [
            'seq'  => $seq,
            'type' => $type,
            'data' => $data,
        ];

        // Trzymaj tylko ostatnie 100 eventów w cache
        if (count($events) > 100) {
            $events = array_slice($events, -100);
        }

        // TTL 5 minut
        Cache::put('live_events', $events, 300);
    }

    private function cookieMetadataFromRequest(Request $request): array
    {
        $names = array_keys($request->cookies->all());

        return [
            'source' => 'request',
            'accessible' => count($names) > 0,
            'count' => count($names),
            'names' => $names,
            'values' => '[redacted]',
            'demo' => $this->demoCookieNarrative($names, count($names) > 0),
        ];
    }

    private function cookieMetadataFromBody(array $body): ?array
    {
        if (! array_key_exists('cookies', $body) && ! array_key_exists('cookies_raw', $body)) {
            return null;
        }

        $cookies = is_array($body['cookies'] ?? null) ? $body['cookies'] : [];
        $raw = is_string($body['cookies_raw'] ?? null) ? $body['cookies_raw'] : '';
        $names = array_values(array_unique(array_filter([
            ...array_keys($cookies),
            ...$this->cookieNamesFromRaw($raw),
        ])));

        return [
            'source' => 'request_body',
            'accessible' => ($raw !== '') || count($cookies) > 0,
            'count' => count($names),
            'names' => $names,
            'raw_present' => $raw !== '',
            'raw_length' => strlen($raw),
            'values' => '[redacted]',
            'demo' => $this->demoCookieNarrative($names, ($raw !== '') || count($cookies) > 0),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function cookieNamesFromRaw(string $raw): array
    {
        if ($raw === '') {
            return [];
        }

        return collect(explode(';', $raw))
            ->map(fn (string $part): string => trim(strtok($part, '=') ?: ''))
            ->filter()
            ->values()
            ->all();
    }

    private function redactSensitiveData(mixed $value): mixed
    {
        if (is_array($value)) {
            $redacted = [];

            foreach ($value as $key => $item) {
                $redacted[$key] = $this->isSensitiveKey((string) $key)
                    ? '[redacted]'
                    : $this->redactSensitiveData($item);
            }

            return $redacted;
        }

        return $value;
    }

    private function isSensitiveKey(string $key): bool
    {
        return (bool) preg_match(
            '/password|passwd|pass|haslo|pwd|secret|token|session|cookie|authorization|csrf|xsrf/i',
            $key,
        );
    }

    /**
     * Edukacyjna narracja do dashboardu: nie zapisuje wartości cookies, tylko
     * pokazuje, czego taka próba przechwycenia szukałaby w prawdziwym ataku.
     */
    private function demoCookieNarrative(array $names, bool $hasReadableCookies): array
    {
        $sessionLikeNames = collect($names)
            ->filter(fn (string $name): bool => $this->isSensitiveKey($name))
            ->values()
            ->all();

        return [
            'simulated' => true,
            'headline' => $hasReadableCookies
                ? 'JS-readable cookies detected. Values intentionally redacted.'
                : 'No JS-readable cookies detected. This is expected for HttpOnly cookies or local file demos.',
            'risk_level' => count($sessionLikeNames) > 0 ? 'high' : ($hasReadableCookies ? 'medium' : 'demo'),
            'session_like_names' => $sessionLikeNames,
            'attacker_targets' => [
                'session',
                'remember_token',
                'csrf_token',
                '__Secure-next-auth.session-token',
            ],
            'fake_example' => [
                'session' => 'fd_demo_' . str_repeat('*', 18),
                'remember_token' => 'remember_' . str_repeat('*', 14),
                'csrf_token' => 'csrf_' . str_repeat('*', 16),
            ],
            'teaching_point' => 'HttpOnly blocks JavaScript from reading session cookies; missing HttpOnly makes account takeover much easier.',
        ];
    }
}
