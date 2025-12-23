<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\System\PlansSeeder;
use Database\Seeders\System\SuperAdminSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(PlansSeeder::class);
        $this->call(SuperAdminSeeder::class);
    }
}
