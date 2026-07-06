<?php
// database/seeders/StationSeeder.php
namespace Database\Seeders;

use App\Models\Station;
use Illuminate\Database\Seeder;

class StationSeeder extends Seeder
{
    public function run(): void
    {
        $stations = [
            ['name' => 'Dhaka', 'code' => 'DA', 'division' => 'Dhaka', 'district' => 'Dhaka'],
            ['name' => 'Chattogram', 'code' => 'CTG', 'division' => 'Chattogram', 'district' => 'Chattogram'],
            ['name' => 'Sylhet', 'code' => 'SYL', 'division' => 'Sylhet', 'district' => 'Sylhet'],
            ['name' => 'Rajshahi', 'code' => 'RJ', 'division' => 'Rajshahi', 'district' => 'Rajshahi'],
            ['name' => 'Khulna', 'code' => 'KH', 'division' => 'Khulna', 'district' => 'Khulna'],
            ['name' => 'Akhaura Junction', 'code' => 'AHA', 'division' => 'Chattogram', 'district' => 'Brahmanbaria'],
            ['name' => 'Cumilla', 'code' => 'CM', 'division' => 'Chattogram', 'district' => 'Cumilla'],
            ['name' => 'Feni', 'code' => 'FE', 'division' => 'Chattogram', 'district' => 'Feni'],
            ['name' => 'Noakhali', 'code' => 'NK', 'division' => 'Chattogram', 'district' => 'Noakhali'],
            ['name' => 'Mymensingh', 'code' => 'MY', 'division' => 'Mymensingh', 'district' => 'Mymensingh'],
            ['name' => 'Rangpur', 'code' => 'RP', 'division' => 'Rangpur', 'district' => 'Rangpur'],
            ['name' => 'Barishal', 'code' => 'BA', 'division' => 'Barishal', 'district' => 'Barishal'],
        ];

        foreach ($stations as $station) {
            Station::updateOrCreate([
                'code' => $station['code'],
            ], $station);
        }
    }
}