<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'qr_code',
        'user_id',
        'pkcs7_signature',
        'signer_name',
        'signer_pinfl',
        'signer_inn',
        'signer_organization',
        'signed_at',
        'signature_info',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
        'signature_info' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($document) {
            if (empty($document->qr_code)) {
                $document->qr_code = Str::uuid()->toString();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isSigned(): bool
    {
        return !empty($this->pkcs7_signature);
    }

    public function getVerificationUrl(): string
    {
        return url("/documents/verify/{$this->qr_code}");
    }
}
