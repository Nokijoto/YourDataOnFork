<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CapturedRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'source',
        'external_type',
        'external_id',
        'ip_address',
        'user_agent',
        'referer',
        'headers',
        'cookie_metadata',
        'payload',
        'request_body',
        'geo',
        'request_method',
        'request_url',
    ];

    protected $casts = [
        'headers' => 'array',
        'cookie_metadata' => 'array',
        'payload' => 'array',
        'request_body' => 'array',
        'geo'     => 'array',
    ];

    /**
     * Zwraca kolorowy badge dla źródła przechwycenia.
     */
    public function getSourceColorAttribute(): string
    {
        return match (strtolower($this->source)) {
            'discord'  => 'indigo',
            'facebook' => 'blue',
            'steam'    => 'cyan',
            'uczelnia' => 'amber',
            default    => 'gray',
        };
    }

    /**
     * Skrócony podgląd payloadu (pierwsze 3 klucze).
     */
    public function getPayloadPreviewAttribute(): string
    {
        if (empty($this->payload)) {
            return '—';
        }

        $parts = [];
        $i = 0;
        foreach ($this->payload as $key => $value) {
            if ($i >= 3) {
                $parts[] = '…';
                break;
            }
            $val = is_string($value) ? mb_strimwidth($value, 0, 20, '…') : json_encode($value);
            $parts[] = "{$key}: {$val}";
            $i++;
        }

        return implode(' | ', $parts);
    }
}
