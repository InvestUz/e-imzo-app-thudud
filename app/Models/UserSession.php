<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    protected $fillable = [
        'user_id', 'session_token', 'ip_address', 'user_agent',
        'logged_in_at', 'last_active_at', 'logged_out_at',
        'is_active', 'auth_method',
    ];

    protected $casts = [
        'logged_in_at'    => 'datetime',
        'last_active_at'  => 'datetime',
        'logged_out_at'   => 'datetime',
        'is_active'       => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Terminate this session */
    public function terminate(): void
    {
        $this->update([
            'is_active'      => false,
            'logged_out_at'  => now(),
        ]);
    }

    /** Short browser name from user agent */
    public function getBrowserAttribute(): string
    {
        $ua = $this->user_agent ?? '';
        if (str_contains($ua, 'Chrome'))  return 'Chrome';
        if (str_contains($ua, 'Firefox')) return 'Firefox';
        if (str_contains($ua, 'Safari'))  return 'Safari';
        if (str_contains($ua, 'Edge'))    return 'Edge';
        if (str_contains($ua, 'Opera'))   return 'Opera';
        return 'Noma\'lum';
    }

    public function getOsAttribute(): string
    {
        $ua = $this->user_agent ?? '';
        if (str_contains($ua, 'Windows')) return 'Windows';
        if (str_contains($ua, 'Mac'))     return 'macOS';
        if (str_contains($ua, 'Linux'))   return 'Linux';
        if (str_contains($ua, 'Android')) return 'Android';
        if (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) return 'iOS';
        return 'Noma\'lum';
    }
}
