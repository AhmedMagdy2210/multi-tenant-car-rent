<?php

namespace Database\Seeders\System;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plan::create([
            'name' => 'Free plan',
            'slug' => 'free-plan',
            'description' => 'This is our free plan',
            'tier' => 'free',
            'price_monthly' => 0,
            'price_yearly' => 0,
            'features' => [
                'max_cars' => 5,
                'max_users' => 5 // For Staff not the customers
            ],
            'limits' => [
                'storage_gb' => 5
            ],
            'is_active' => true,
            'is_default' => true
        ]);
        Plan::create([
            'name' => 'Starter Plan',
            'slug' => 'starter-plan',
            'description' => 'This is our free plan',
            'tier' => 'starter',
            'price_monthly' => 10,
            'price_yearly' => 120,
            'currency' => 'USD',
            'features' => [
                'max_cars' => 20,
                'max_users' => 20
            ],
            'limits' => [
                'storage_gb' => 10
            ],
            'is_active' => true,
            'is_default' => false
        ]);

    }
}
