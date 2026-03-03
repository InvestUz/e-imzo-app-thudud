<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Application extends Model
{
    protected $fillable = [
        'number', 'applicant_id', 'district_id',
        'cadastral_number', 'address', 'area_sqm',
        'source', 'description', 'form_data', 'status', 'current_step',
        'applicant_pkcs7', 'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'form_data'    => 'array',
    ];

    // Workflow step order
    public const STEPS = [
        1 => 'moderator',
        2 => 'complaint_officer',
        3 => 'lawyer',
        4 => 'executor',
        5 => 'district_head',
    ];

    public const STATUS_LABELS = [
        'pending'          => 'Kutilmoqda',
        'moderator_review' => 'Moderator ko\'rib chiqmoqda',
        'complaint_review' => 'Shikoyat bo\'limi ko\'rib chiqmoqda',
        'legal_review'     => 'Yurist ko\'rib chiqmoqda',
        'executor_review'  => 'Ijrochi ko\'rib chiqmoqda',
        'head_review'      => 'Rahbar ko\'rib chiqmoqda',
        'approved'         => 'Tasdiqlandi',
        'rejected'         => 'Rad etildi',
    ];

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ApplicationDocument::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(ApplicationApproval::class)->orderBy('step_order');
    }

    public function calculation(): HasOne
    {
        return $this->hasOne(Calculation::class);
    }

    public function dalolatnomaSignatures(): HasMany
    {
        return $this->hasMany(DalolatnomaSignature::class);
    }

    public function currentApproval()
    {
        return $this->approvals()->where('status', 'pending')->orderBy('step_order')->first();
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function isEditable(): bool
    {
        return $this->status === 'pending';
    }

    // Generate application number: ARZ-2026-00001
    public static function generateNumber(): string
    {
        $year = now()->year;
        $last = static::whereYear('created_at', $year)->count();
        return 'ARZ-' . $year . '-' . str_pad($last + 1, 5, '0', STR_PAD_LEFT);
    }
}
