<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'project_id',
        'created_by',
        'quote_number',
        'title',
        'status',
        'currency',
        'tax_rate',
        'tax_included',
        'subtotal',
        'tax_amount',
        'total',
        'valid_until',
        'notes',
        'terms',
        'sent_at',
        'approved_at',
        'rejected_at',
    ];

    protected $casts = [
        'tax_included'  => 'boolean',
        'valid_until'   => 'date',
        'sent_at'       => 'datetime',
        'approved_at'   => 'datetime',
        'rejected_at'   => 'datetime',
        'subtotal'      => 'integer',
        'tax_amount'    => 'integer',
        'total'         => 'integer',
    ];

    // ── Relaciones ──────────────────────────────────────────────

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class)->orderBy('order');
    }

    // ── Accessors ───────────────────────────────────────────────

    public function getStatusNameAttribute(): string
    {
        return match ($this->status) {
            'draft'    => 'Borrador',
            'sent'     => 'Enviada',
            'approved' => 'Aprobada',
            'rejected' => 'Rechazada',
            default    => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft'    => 'gray',
            'sent'     => 'blue',
            'approved' => 'green',
            'rejected' => 'red',
            default    => 'gray',
        };
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return $this->formatAmount($this->subtotal);
    }

    public function getFormattedTaxAmountAttribute(): string
    {
        return $this->formatAmount($this->tax_amount);
    }

    public function getFormattedTotalAttribute(): string
    {
        return $this->formatAmount($this->total);
    }

    private function formatAmount(int $amount): string
    {
        return match ($this->currency) {
            'USD'   => 'USD ' . number_format($amount / 100, 2, '.', ','),
            default => '$' . number_format($amount, 0, ',', '.'),
        };
    }

    public function getIsEditableAttribute(): bool
    {
        return $this->status === 'draft';
    }

    // ── Scopes ──────────────────────────────────────────────────

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('quote_number', 'like', "%{$term}%")
              ->orWhereHas('client', fn ($c) => $c->where('name', 'like', "%{$term}%"));
        });
    }

    // ── Helpers ─────────────────────────────────────────────────

    /**
     * Recalcula subtotal, tax_amount y total desde los items.
     */
    public function recalculateTotals(): void
    {
        $subtotal   = $this->items()->sum('subtotal');
        $taxAmount  = (int) round($subtotal * ($this->tax_rate / 100));

        $this->update([
            'subtotal'   => $subtotal,
            'tax_amount' => $taxAmount,
            'total'      => $subtotal + $taxAmount,
        ]);
    }

    /**
     * Marca como enviada y registra timestamp.
     */
    public function markAsSent(): void
    {
        $this->update(['status' => 'sent', 'sent_at' => now()]);
    }

    /**
     * Aprueba la cotización y crea el proyecto asociado.
     */
    public function approve(): Project
    {
        $this->update(['status' => 'approved', 'approved_at' => now()]);

        $project = Project::create([
            'client_id'           => $this->client_id,
            'user_id'             => $this->created_by,
            'name'                => $this->title,
            'description'         => "Proyecto generado desde cotización {$this->quote_number}.",
            'status'              => 'draft',
            'budget_amount'       => $this->total,
            'currency'            => $this->currency,
            'budget_includes_vat' => true,
        ]);

        $this->update(['project_id' => $project->id]);

        return $project;
    }

    /**
     * Rechaza la cotización.
     */
    public function reject(): void
    {
        $this->update(['status' => 'rejected', 'rejected_at' => now()]);
    }

    /**
     * Genera el número de cotización automático: COT-YYYY-NNN
     */
    public static function generateNumber(): string
    {
        $year  = now()->year;
        $count = static::whereYear('created_at', $year)->withTrashed()->count() + 1;

        return sprintf('COT-%d-%03d', $year, $count);
    }
}
