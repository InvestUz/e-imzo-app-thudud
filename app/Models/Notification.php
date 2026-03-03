<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id', 'type', 'title', 'body', 'data',
        'related_type', 'related_id', 'read_at', 'created_by',
    ];

    protected $casts = [
        'data'    => 'array',
        'read_at' => 'datetime',
    ];

    // Notification types
    const TYPE_APP_SUBMITTED  = 'app_submitted';
    const TYPE_STEP_APPROVED  = 'step_approved';
    const TYPE_STEP_REJECTED  = 'step_rejected';
    const TYPE_APP_APPROVED   = 'app_approved';
    const TYPE_APP_REJECTED   = 'app_rejected';
    const TYPE_DALO_SIGNED    = 'dalo_signed';
    const TYPE_SYSTEM         = 'system';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function markRead(): void
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    /** Helper: send a notification to a user */
    public static function send(
        int    $userId,
        string $type,
        string $title,
        string $body = '',
        array  $data = [],
        ?int   $createdBy = null,
        ?string $relatedType = null,
        ?int    $relatedId = null
    ): self {
        return self::create([
            'user_id'      => $userId,
            'type'         => $type,
            'title'        => $title,
            'body'         => $body,
            'data'         => $data ?: null,
            'related_type' => $relatedType,
            'related_id'   => $relatedId,
            'created_by'   => $createdBy,
        ]);
    }

    public function getIconAttribute(): string
    {
        return match($this->type) {
            self::TYPE_APP_SUBMITTED  => '📥',
            self::TYPE_STEP_APPROVED  => '✅',
            self::TYPE_STEP_REJECTED  => '❌',
            self::TYPE_APP_APPROVED   => '🎉',
            self::TYPE_APP_REJECTED   => '🚫',
            self::TYPE_DALO_SIGNED    => '✍️',
            default                   => '🔔',
        };
    }

    public function getColorClassAttribute(): string
    {
        return match($this->type) {
            self::TYPE_APP_SUBMITTED  => 'sbadge-info',
            self::TYPE_STEP_APPROVED  => 'sbadge-success',
            self::TYPE_STEP_REJECTED  => 'sbadge-danger',
            self::TYPE_APP_APPROVED   => 'sbadge-success',
            self::TYPE_APP_REJECTED   => 'sbadge-danger',
            self::TYPE_DALO_SIGNED    => 'sbadge-purple',
            default                   => 'sbadge-gray',
        };
    }
}
