<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Plan extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    protected $casts = [
        'features' => 'array',
        'limits' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean'
    ];

    protected $attributes = [
        'is_active' => true,
        'is_default' => false,
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected static function booted()
    {
        static::updating(function ($plan) {
            if ($plan->isDirty('slug')) {
                Cache::forget("plan.{$plan->getOriginal('slug')}");
            }
        });
        static::saved(function ($plan) {
            Cache::forget('plans');
            Cache::forget("plan.$plan->slug");
        });
        static::deleted(function ($plan) {
            Cache::forget('plans');
            Cache::forget("plan.$plan->slug");
        });
    }


}
