<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id', 'concept', 'quantity', 'unit_price', 'total', 'type'
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
}
