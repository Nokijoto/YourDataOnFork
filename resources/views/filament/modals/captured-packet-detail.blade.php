<div class="space-y-4 p-2 font-mono text-sm">

    {{-- Header --}}
    <div class="flex items-center gap-3 pb-3 border-b border-gray-200 dark:border-gray-700">
        <span class="px-2 py-0.5 rounded text-xs font-bold uppercase
            {{ match(strtoupper($record->protocol)) {
                'HTTP'  => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
                'HTTPS' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                'DNS'   => 'bg-lime-100 text-lime-800 dark:bg-lime-900 dark:text-lime-300',
                'TCP'   => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-300',
                'UDP'   => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                'ICMP'  => 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-300',
                'ARP'   => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
            } }}">
            {{ $record->protocol }}
        </span>
        <span class="text-gray-500 text-xs">{{ $record->created_at->format('d.m.Y H:i:s') }}</span>
        <span class="ml-auto text-gray-400 text-xs">#{{ $record->id }}</span>
    </div>

    {{-- Flow --}}
    <div class="bg-black rounded p-4 text-center">
        <div class="text-lg">
            <span class="text-green-400 font-bold">{{ $record->src_ip ?? '?' }}{{ $record->src_port ? ':' . $record->src_port : '' }}</span>
            <span class="text-gray-500 mx-3">──────►</span>
            <span class="text-red-400 font-bold">{{ $record->dst_ip ?? '?' }}{{ $record->dst_port ? ':' . $record->dst_port : '' }}</span>
        </div>
        <div class="text-gray-400 text-xs mt-2">{{ $record->summary }}</div>
    </div>

    {{-- Packet Metadata --}}
    <div class="grid grid-cols-3 gap-3">
        <div class="bg-gray-50 dark:bg-gray-800 rounded p-3">
            <div class="text-xs text-gray-400 uppercase mb-1">Rozmiar</div>
            <div class="text-cyan-400 font-bold">{{ $record->packet_size }} B</div>
        </div>
        <div class="bg-gray-50 dark:bg-gray-800 rounded p-3">
            <div class="text-xs text-gray-400 uppercase mb-1">TTL</div>
            <div class="text-yellow-400 font-bold">{{ $record->ttl ?? '—' }}</div>
        </div>
        <div class="bg-gray-50 dark:bg-gray-800 rounded p-3">
            <div class="text-xs text-gray-400 uppercase mb-1">Flagi TCP</div>
            <div class="text-red-400 font-bold">{{ $record->flags ?? '—' }}</div>
        </div>
        <div class="bg-gray-50 dark:bg-gray-800 rounded p-3">
            <div class="text-xs text-gray-400 uppercase mb-1">Interfejs</div>
            <div class="text-gray-300">{{ $record->interface ?? '—' }}</div>
        </div>
    </div>

    {{-- Payload Preview --}}
    @if($record->payload_preview)
        <div>
            <div class="text-xs text-orange-400 uppercase font-bold mb-2">Payload (HEX / pierwsze 256 B)</div>
            <div class="bg-black rounded p-3 text-xs text-green-300 break-all leading-relaxed max-h-32 overflow-y-auto">
                {{ $record->payload_preview }}
            </div>
        </div>
    @endif

    {{-- Raw JSON --}}
    @if(!empty($record->raw))
        <div>
            <div class="text-xs text-gray-400 uppercase font-bold mb-2">Raw Data (JSON)</div>
            <div class="bg-black rounded p-3 text-xs text-gray-300 max-h-40 overflow-y-auto">
                <pre>{{ json_encode($record->raw, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
    @endif

</div>
