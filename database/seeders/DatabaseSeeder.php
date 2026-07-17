<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            StationSeeder::class,
        ]);

        \App\Models\User::updateOrCreate(
            ['email' => 'ullas@gmail.com'],
            [
                'name' => 'Admin Ullas',
                'password' => \Illuminate\Support\Facades\Hash::make('2207086'),
                'role' => \App\Enums\UserRole::ADMIN,
                'email_verified_at' => now(),
            ]
        );
    }
}