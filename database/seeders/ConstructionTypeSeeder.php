<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ConstructionType;

class ConstructionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $constructionTypes = [
            [
                'name' => 'Commercial',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Residential',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Office & Retail',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Residential & Commercial',
                'is_active' => true,
                'user_id' => 1,
            ],
        ];

        foreach ($constructionTypes as $type) {
            ConstructionType::create($type);
        }
    }
}



