<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CapturedPacket extends Model
{
    use HasFactory;

    protected $fillable = [
        'interface',
        'protocol',
        'src_ip',
        'dst_ip',
        'src_port',
        'dst_port',
        'packet_size',
        'ttl',
        'flags',
        'payload_preview',
        'summary',
        'raw',
    ];

    protected $casts = [
        'raw'      => 'array',
        'src_port' => 'integer',
        'dst_port' => 'integer',
        'ttl'      => 'integer',
    ];

    /**
     * Kolor badge dla protokołu.
     */
    public function getProtocolColorAttribute(): string
    {
        return match (strtoupper($this->protocol)) {
            'HTTP'  => 'orange',
            'HTTPS' => 'green',
            'DNS'   => 'lime',
            'TCP'   => 'cyan',
            'UDP'   => 'yellow',
            'ICMP'  => 'pink',
            'ARP'   => 'purple',
            default => 'gray',
        };
    }

    /**
     * Czytelny zapis kierunku pakietu.
     */
    public function getFlowAttribute(): string
    {
        $src = $this->src_ip . ($this->src_port ? ":{$this->src_port}" : '');
        $dst = $this->dst_ip . ($this->dst_port ? ":{$this->dst_port}" : '');
        return "{$src} → {$dst}";
    }
}
