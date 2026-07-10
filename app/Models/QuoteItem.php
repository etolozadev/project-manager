<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteItem extends Model
{
    protected $fillable = [
        'quote_id',
        'description',
        'detail',
        'quantity',
        'unit_price',
        'subtotal',
        'order',
    ];

    protected $casts = [
        'quantity'   => 'integer',
        'unit_price' => 'integer',
        'subtotal'   => 'integer',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function getFormattedUnitPriceAttribute(): string
    {
        return '$' . number_format($this->unit_price, 0, ',', '.');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return '$' . number_format($this->subtotal, 0, ',', '.');
    }

    // Calcula el subtotal automáticamente al guardar
    protected static function booted(): void
    {
        static::saving(function (QuoteItem $item) {
            $item->subtotal = $item->quantity * $item->unit_price;
        });

        static::saved(function (QuoteItem $item) {
            $item->quote->recalculateTotals();
        });

        static::deleted(function (QuoteItem $item) {
            $item->quote->recalculateTotals();
        });
    }
}
