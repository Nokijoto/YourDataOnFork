<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PwnedRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'breach_id',
        'is_pwned',
        'custom_password',
        'custom_username',
        'custom_phone',
        'custom_name',
    ];

    protected $casts = [
        'is_pwned' => 'boolean',
    ];

    public function breach(): BelongsTo
    {
        return $this->belongsTo(PwnedBreach::class, 'breach_id');
    }
}
