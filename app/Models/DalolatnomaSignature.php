<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DalolatnomaSignature extends Model
{
    protected $fillable = [
        'application_id',
        'commission_position',
        'signed_by',
        'pkcs7_signature',
        'qr_code',
        'signed_at',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    // ─── All 10 commission positions ───────────────────────────────────
    public const POSITIONS = [
        'hokim_qurilish'   => "Hokim o'rinbosari (Qurilish)",
        'qurilish'         => "Qurilish bo'limi",
        'ekologiya'        => "Ekologiya bo'limi",
        'obodonlashtirish' => "Obodonlashtirish",
        'kadastr'          => "Kadastr agentligi",
        'fvv'              => "FVV (ChS) bo'limi",
        'ses'              => "Sanepidqo'mita (SES)",
        'soliq'            => "Soliq inspeksiyasi",
        'iib'              => "IIB vakili",
        'yordamchi'        => "Hokim yordamchisi",
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function signer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signed_by');
    }

    public function positionLabel(): string
    {
        return self::POSITIONS[$this->commission_position] ?? $this->commission_position;
    }

    public function getVerifyUrl(): string
    {
        return route('dalolatnoma.verify', $this->qr_code);
    }
}
