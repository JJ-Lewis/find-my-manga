<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Retailer extends Model
{
    protected $fillable = [
        'name',
        'type',
        'base_url',
        'config',
    ];

    protected $casts = [
        'config' => 'array',
    ];

    public function products()
    {
        return $this->hasMany(RetailerProduct::class);
    }
}
