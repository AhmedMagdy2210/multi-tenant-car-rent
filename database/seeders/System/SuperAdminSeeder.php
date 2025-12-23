<?php

namespace Database\Seeders\System;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'username' => 'super_admin',
            'email' => 'super_admin@test.com',
            'email_verified_at' => now(),
            'phone' => '0123456789',
            'phone_verified_at' => now(),
            'role' => 'super_admin',
            'password' => Hash::make('password')
        ]);
    }
}
