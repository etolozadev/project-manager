<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'project_id',
        'quote_id',
        'created_by',
        'amount',
        'currency',
        'payment_date',
        'method',
        'reference',
        'notes',
    ];

    protected $casts = [
        'amount'       => 'integer',
        'payment_date' => 'date',
    ];

    // ── Relaciones ──────────────────────────────────────────────

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Accessors ───────────────────────────────────────────────

    public function getFormattedAmountAttribute(): string
    {
        return match ($this->currency) {
            'USD'   => 'USD ' . number_format($this->amount, 2, '.', ','),
            default => '$' . number_format($this->amount, 0, ',', '.'),
        };
    }

    public function getMethodNameAttribute(): string
    {
        return match ($this->method) {
            'transfer' => 'Transferencia',
            'cash'     => 'Efectivo',
            'check'    => 'Cheque',
            'card'     => 'Tarjeta',
            'other'    => 'Otro',
            default    => $this->method,
        };
    }

    public function getMethodColorAttribute(): string
    {
        return match ($this->method) {
            'transfer' => 'blue',
            'cash'     => 'green',
            'check'    => 'yellow',
            'card'     => 'purple',
            default    => 'gray',
        };
    }
}
