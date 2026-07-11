<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = [
        'project_id',
        'created_by',
        'category',
        'description',
        'amount',
        'currency',
        'expense_date',
        'notes',
    ];

    protected $casts = [
        'amount'       => 'integer',
        'expense_date' => 'date',
    ];

    // ── Relaciones ──────────────────────────────────────────────

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
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

    public function getCategoryNameAttribute(): string
    {
        return match ($this->category) {
            'hosting'     => 'Hosting / VPS',
            'domain'      => 'Dominio',
            'license'     => 'Licencia',
            'subcontract' => 'Subcontrato',
            'tools'       => 'Herramientas',
            'travel'      => 'Viáticos',
            'other'       => 'Otro',
            default       => $this->category,
        };
    }

    public function getCategoryColorAttribute(): string
    {
        return match ($this->category) {
            'hosting'     => 'blue',
            'domain'      => 'indigo',
            'license'     => 'purple',
            'subcontract' => 'orange',
            'tools'       => 'yellow',
            'travel'      => 'green',
            default       => 'gray',
        };
    }
}
