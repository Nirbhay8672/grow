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
        // Get construction types
        $commercial = ConstructionType::where('name', 'Commercial')->first();
        $residential = ConstructionType::where('name', 'Residential')->first();
        
        $categories = [];
        
        // Commercial Categories
        if ($commercial) {
            $categories[] = [
                'name' => 'Office',
                'is_active' => true,
                'user_id' => 1,
                'construction_type_id' => $commercial->id,
            ];
            $categories[] = [
                'name' => 'Retail',
                'is_active' => true,
                'user_id' => 1,
                'construction_type_id' => $commercial->id,
            ];
            $categories[] = [
                'name' => 'Storage/Industrial',
                'is_active' => true,
                'user_id' => 1,
                'construction_type_id' => $commercial->id,
            ];
        }
        
        // Residential Categories
        if ($residential) {
            $categories[] = [
                'name' => 'Flat',
                'is_active' => true,
                'user_id' => 1,
                'construction_type_id' => $residential->id,
            ];
            $categories[] = [
                'name' => 'Villa/Bungalow',
                'is_active' => true,
                'user_id' => 1,
                'construction_type_id' => $residential->id,
            ];
            $categories[] = [
                'name' => 'Plot',
                'is_active' => true,
                'user_id' => 1,
                'construction_type_id' => $residential->id,
            ];
            $categories[] = [
                'name' => 'Penthouse',
                'is_active' => true,
                'user_id' => 1,
                'construction_type_id' => $residential->id,
            ];
        }

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}

