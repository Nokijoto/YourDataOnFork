<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SherlockRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'service_id',
        'is_found',
    ];

    protected $casts = [
        'is_found' => 'boolean',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(SherlockService::class, 'service_id');
    }
}
