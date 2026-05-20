<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PwnedBreach extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'breach_date',
        'compromised_data',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function rules(): HasMany
    {
        return $this->hasMany(PwnedRule::class, 'breach_id');
    }
}
