<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Amenity;

class AmenitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $amenities = [
            [
                'name' => 'Swimming Pool',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Gymnasium',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Parking',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Security',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Lift/Elevator',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Power Backup',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Garden',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Club House',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Playground',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'CCTV Surveillance',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Wi-Fi',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Water Supply',
                'is_active' => true,
                'user_id' => 1,
            ],
        ];

        foreach ($amenities as $amenity) {
            Amenity::create($amenity);
        }
    }
}

