<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $guarded = [];

    protected $casts = [
        'features' => 'array',
        'limits' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean'
    ];
}
