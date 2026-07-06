<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed stations first (required for station_master registration)
        $this->call(StationSeeder::class);

        // Create admin user for testing
        User::updateOrCreate(
            ['email' => 'admin@trackrail.com'],
            [
                'name'     => 'Admin',
                'email'    => 'admin@trackrail.com',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );

        // Create a test passenger
        User::updateOrCreate(
            ['email' => 'passenger@trackrail.com'],
            [
                'name'     => 'Test Passenger',
                'email'    => 'passenger@trackrail.com',
                'password' => Hash::make('password'),
                'role'     => 'passenger',
            ]
        );
    }
}
