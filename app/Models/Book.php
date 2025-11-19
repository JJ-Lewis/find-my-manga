<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'authors',
        'isbn_10',
        'isbn_13',
        'publisher',
        'published_at',
        'metadata',
    ];

    protected $casts = [
        'authors'      => 'array',
        'published_at' => 'date',
        'metadata'     => 'array',
    ];

    public function retailerProducts()
    {
        return $this->hasMany(RetailerProduct::class);
    }
}
