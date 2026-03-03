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
        'name', 'email', 'password',
        'pinfl', 'inn', 'organization', 'position',
        'serial_number', 'certificate_valid_from', 'certificate_valid_to',
        'role', 'commission_position', 'district_id', 'is_regional_backup',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at'      => 'datetime',
        'password'               => 'hashed',
        'certificate_valid_from' => 'datetime',
        'certificate_valid_to'   => 'datetime',
        'is_regional_backup'     => 'boolean',
    ];

    // --- Role helpers ---
    public function isConsumer(): bool      { return $this->role === 'consumer'; }
    public function isAdmin(): bool         { return $this->role === 'admin'; }
    public function isCommission(): bool    { return $this->role === 'commission'; }
    public function isStaff(): bool         { return !in_array($this->role, ['consumer']); }
    public function hasRole(string $role): bool { return $this->role === $role; }

    // Can this user approve a specific step for a given district?
    public function canApproveStep(string $stepRole, int $districtId): bool
    {
        if ($this->role !== $stepRole) return false;
        // Regional backup can approve for any district
        if ($this->is_regional_backup) return true;
        // Otherwise must be same district
        return (int) $this->district_id === $districtId;
    }

    // --- Relationships ---
    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'applicant_id');
    }

    public function userSessions(): HasMany
    {
        return $this->hasMany(UserSession::class);
    }

    public function activeSessions(): HasMany
    {
        return $this->hasMany(UserSession::class)->where('is_active', true);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function unreadNotificationsCount(): int
    {
        return $this->notifications()->whereNull('read_at')->count();
    }

    public function isCertificateValid(): bool
    {
        if (!$this->certificate_valid_to) return false;
        return now()->lt($this->certificate_valid_to);
    }
}
