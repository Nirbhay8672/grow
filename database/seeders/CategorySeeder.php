<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\ConstructionType;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get construction types to assign to categories
        $constructionTypes = ConstructionType::pluck('id')->toArray();
        
        $categories = [
            [
                'name' => 'Apartment',
                'is_active' => true,
                'user_id' => 1,
                'construction_type_id' => $constructionTypes[3] ?? null, // Residential
            ],
            [
                'name' => 'Villa',
                'is_active' => true,
                'user_id' => 1,
                'construction_type_id' => $constructionTypes[3] ?? null, // Residential
            ],
            [
                'name' => 'Plot',
                'is_active' => true,
                'user_id' => 1,
                'construction_type_id' => $constructionTypes[4] ?? null, // Residential & Commercial
            ],
            [
                'name' => 'Shop',
                'is_active' => true,
                'user_id' => 1,
                'construction_type_id' => $constructionTypes[1] ?? null, // Commercial
            ],
            [
                'name' => 'Office Space',
                'is_active' => true,
                'user_id' => 1,
                'construction_type_id' => $constructionTypes[2] ?? null, // Office & Retail
            ],
            [
                'name' => 'Warehouse',
                'is_active' => true,
                'user_id' => 1,
                'construction_type_id' => $constructionTypes[0] ?? null, // Industrial
            ],
            [
                'name' => 'Farm House',
                'is_active' => true,
                'user_id' => 1,
                'construction_type_id' => $constructionTypes[3] ?? null, // Residential
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}

