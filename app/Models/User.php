<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'pinfl',
        'inn',
        'organization',
        'position',
        'serial_number',
        'certificate_valid_from',
        'certificate_valid_to',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'certificate_valid_from' => 'datetime',
        'certificate_valid_to' => 'datetime',
    ];

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function isCertificateValid(): bool
    {
        if (!$this->certificate_valid_to) {
            return false;
        }
        return now()->lt($this->certificate_valid_to);
    }
}
