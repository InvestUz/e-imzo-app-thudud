<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationApproval extends Model
{
    protected $fillable = [
        'application_id', 'step_order', 'step_role',
        'assigned_to', 'approved_by', 'is_backup_approval',
        'status', 'comments', 'pkcs7_signature', 'approved_at',
    ];

    protected $casts = [
        'approved_at'       => 'datetime',
        'is_backup_approval'=> 'boolean',
    ];

    public const ROLE_LABELS = [
        'moderator'        => 'Moderator',
        'complaint_officer'=> 'Shikoyat mutaxassisi',
        'lawyer'           => 'Yurist',
        'executor'         => 'Ijrochi',
        'district_head'    => 'Tuman boshlig\'i',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function roleLabel(): string
    {
        return self::ROLE_LABELS[$this->step_role] ?? $this->step_role;
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
