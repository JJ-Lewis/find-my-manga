<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RetailerProduct extends Model
{
    protected $fillable = [
        'retailer_id',
        'book_id',
        'sku',
        'url',
        'price_cents',
        'currency',
        'in_stock',
        'stock_qty',
        'raw_response',
        'last_checked_at',
    ];

    protected $casts = [
        'in_stock'       => 'boolean',
        'raw_response'   => 'array',
        'last_checked_at'=> 'datetime',
    ];

    public function retailer()
    {
        return $this->belongsTo(Retailer::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
