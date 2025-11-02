<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PropertyZone;

class PropertyZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $propertyZones = [
            [
                'name' => 'Residential Zone',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Commercial Zone',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Industrial Zone',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Mixed Use Zone',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Agricultural Zone',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Institutional Zone',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Special Economic Zone (SEZ)',
                'is_active' => true,
                'user_id' => 1,
            ],
        ];

        foreach ($propertyZones as $propertyZone) {
            PropertyZone::create($propertyZone);
        }
    }
}

