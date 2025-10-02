<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $fillable = [
        'email',
        'code',
        'expires_at',
        'verified'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified' => 'boolean'
    ];

    /**
     * Verifica si el OTP ha expirado
     */
    public function isExpired(): bool
    {
        return $this->expires_at < now();
    }

    /**
     * Verifica si el OTP es válido
     */
    public function isValid(): bool
    {
        return !$this->verified && !$this->isExpired();
    }
}
