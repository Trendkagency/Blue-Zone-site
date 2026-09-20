<?php

namespace Database\Seeders;

use App\Models\Mr\GpsConfig;
use Illuminate\Database\Seeder;

class MrGpsConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        GpsConfig::updateOrCreate(
            ['user_id' => null],
            [
                'allowed_radius_m' => 150,
                'is_gps_required' => true,
                'mock_detection_enabled' => true,
            ]
        );
    }
}
