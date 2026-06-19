<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Admin accounts are not registered through the website — they are
     * created manually by running this seeder (or via `php artisan tinker`).
     */
    public function run(): void
    {
        Admin::firstOrCreate(
            ['email' => 'admin@foodhub.test'],
            [
                'name' => 'FoodHub Admin',
                'password' => Hash::make('password'),
            ]
        );
    }
}
