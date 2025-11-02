<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubCategory;
use App\Models\Category;

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories to assign sub categories
        $apartmentCategory = Category::where('name', 'Apartment')->first();
        $villaCategory = Category::where('name', 'Villa')->first();
        $plotCategory = Category::where('name', 'Plot')->first();
        $shopCategory = Category::where('name', 'Shop')->first();
        $officeCategory = Category::where('name', 'Office Space')->first();
        $warehouseCategory = Category::where('name', 'Warehouse')->first();

        $subCategories = [];

        // Apartment sub categories
        if ($apartmentCategory) {
            $subCategories[] = [
                'name' => '1 BHK',
                'category_id' => $apartmentCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => '2 BHK',
                'category_id' => $apartmentCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => '3 BHK',
                'category_id' => $apartmentCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => '4 BHK',
                'category_id' => $apartmentCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
        }

        // Villa sub categories
        if ($villaCategory) {
            $subCategories[] = [
                'name' => '2 BHK Villa',
                'category_id' => $villaCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => '3 BHK Villa',
                'category_id' => $villaCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => '4 BHK Villa',
                'category_id' => $villaCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => '5+ BHK Villa',
                'category_id' => $villaCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
        }

        // Plot sub categories
        if ($plotCategory) {
            $subCategories[] = [
                'name' => 'Residential Plot',
                'category_id' => $plotCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => 'Commercial Plot',
                'category_id' => $plotCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => 'Agricultural Plot',
                'category_id' => $plotCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
        }

        // Shop sub categories
        if ($shopCategory) {
            $subCategories[] = [
                'name' => 'Ground Floor Shop',
                'category_id' => $shopCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => 'First Floor Shop',
                'category_id' => $shopCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => 'Showroom',
                'category_id' => $shopCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
        }

        // Office Space sub categories
        if ($officeCategory) {
            $subCategories[] = [
                'name' => 'Small Office (Up to 500 sq ft)',
                'category_id' => $officeCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => 'Medium Office (500-2000 sq ft)',
                'category_id' => $officeCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => 'Large Office (2000+ sq ft)',
                'category_id' => $officeCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
        }

        // Warehouse sub categories
        if ($warehouseCategory) {
            $subCategories[] = [
                'name' => 'Small Warehouse',
                'category_id' => $warehouseCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => 'Medium Warehouse',
                'category_id' => $warehouseCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => 'Large Warehouse',
                'category_id' => $warehouseCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
        }

        foreach ($subCategories as $subCategory) {
            SubCategory::create($subCategory);
        }
    }
}

