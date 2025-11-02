<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FurnitureType;

class FurnitureTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $furnitureTypes = [
            [
                'name' => 'Fully Furnished',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Semi Furnished',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Unfurnished',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Furnished with Appliances',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Partially Furnished',
                'is_active' => true,
                'user_id' => 1,
            ],
        ];

        foreach ($furnitureTypes as $furnitureType) {
            FurnitureType::create($furnitureType);
        }
    }
}

