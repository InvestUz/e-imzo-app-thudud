<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Calculation extends Model
{
    protected $fillable = [
        'application_id', 'calculated_by',
        'payer_name', 'payer_pinfl',
        'area_sqm', 'rate_per_sqm', 'total_amount',
        'penalty_amount', 'paid_amount',
        'payment_deadline', 'payment_period', 'notes',
    ];

    protected $casts = [
        'payment_deadline' => 'date',
        'area_sqm'         => 'float',
        'rate_per_sqm'     => 'float',
        'total_amount'     => 'float',
        'penalty_amount'   => 'float',
        'paid_amount'      => 'float',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function calculator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'calculated_by');
    }

    public function remainingAmount(): float
    {
        return max(0, ($this->total_amount ?? 0) + ($this->penalty_amount ?? 0) - ($this->paid_amount ?? 0));
    }
}
