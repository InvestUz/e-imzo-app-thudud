<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationDocument extends Model
{
    protected $fillable = [
        'application_id', 'uploaded_by', 'type',
        'original_name', 'path', 'mime_type', 'size', 'notes',
    ];

    public const TYPES = [
        'application_letter' => 'Ariza xati',
        'contract'           => 'Shartnoma',
        'other'              => 'Boshqa',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }
}
