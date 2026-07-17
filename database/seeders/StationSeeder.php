<?php
namespace Database\Seeders;

use App\Models\Station;
use Illuminate\Database\Seeder;

class StationSeeder extends Seeder
{
    public function run(): void
    {
        $stations = [
            ['name' => 'Rangpur', 'code' => 'RP', 'division' => 'Rangpur', 'district' => 'Rangpur'],
            ['name' => 'Rajshahi', 'code' => 'RJ', 'division' => 'Rajshahi', 'district' => 'Rajshahi'],
            ['name' => 'Khulna', 'code' => 'KH', 'division' => 'Khulna', 'district' => 'Khulna'],
            ['name' => 'Barishal', 'code' => 'BA', 'division' => 'Barishal', 'district' => 'Barishal'],
            ['name' => 'Mymensingh', 'code' => 'MY', 'division' => 'Mymensingh', 'district' => 'Mymensingh'],
            ['name' => 'Dhaka', 'code' => 'DA', 'division' => 'Dhaka', 'district' => 'Dhaka'],
            ['name' => 'Sylhet', 'code' => 'SYL', 'division' => 'Sylhet', 'district' => 'Sylhet'],
            ['name' => 'Cumilla', 'code' => 'CM', 'division' => 'Chattogram', 'district' => 'Cumilla'],
            ['name' => 'Feni', 'code' => 'FE', 'division' => 'Chattogram', 'district' => 'Feni'],
            ['name' => 'Noakhali', 'code' => 'NK', 'division' => 'Chattogram', 'district' => 'Noakhali'],
            ['name' => 'Chattogram', 'code' => 'CTG', 'division' => 'Chattogram', 'district' => 'Chattogram'],
        ];

        // Delete stations not in this list to strictly enforce the allowed 11
        $codes = array_column($stations, 'code');
        Station::whereNotIn('code', $codes)->delete();

        foreach ($stations as $station) {
            Station::updateOrCreate([
                'code' => $station['code'],
            ], $station);
        }
    }
}
