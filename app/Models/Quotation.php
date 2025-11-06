<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'title',
        'description',
        'delivery_date',
        'subtotal',
        'discount',
        'tax',
        'total',
    ];

    protected $casts = [
        'delivery_date' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function quote_items()
    {
        return $this->hasMany(QuotationItem::class);
    }
}
