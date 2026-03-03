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

    // Workflow step order (Shartnoma — VM 478)
    // 1.Devon → 2.Ijrochi → 3.Rahbar → 4.Tuman Vakili → 5.Yurist → 6.Komplayans → 7.Rahbar (Yakuniy)
    public const STEPS = [
        1 => 'devon',
        2 => 'executor',
        3 => 'director',
        4 => 'district_rep',
        5 => 'lawyer',
        6 => 'compliance',
        7 => 'director_final',
    ];

    public const STATUS_LABELS = [
        'pending'               => 'Kutilmoqda',
        'devon_review'          => 'Devon qabul qilmoqda',
        'executor_review'       => 'Ijrochi ko\'rib chiqmoqda',
        'director_review'       => 'Rahbar topshiriq bermoqda',
        'district_rep_review'   => 'Tuman vakili ko\'rib chiqmoqda',
        'legal_review'          => 'Yurist ko\'rib chiqmoqda',
        'compliance_review'     => 'Komplayans ko\'rib chiqmoqda',
        'director_final_review' => 'Rahbar yakuniy tasdiqlash',
        'approved'              => 'Tasdiqlandi — Shartnoma tuziladi',
        'rejected'              => 'Rad etildi',
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
