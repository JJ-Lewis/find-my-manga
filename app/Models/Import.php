<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    protected $fillable = [
        'user_id',
        'source',
        'status',
        'file_path',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];
}
