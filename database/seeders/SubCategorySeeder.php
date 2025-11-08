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
        $officeCategory = Category::where('name', 'Office')->first();
        $retailCategory = Category::where('name', 'Retail')->first();
        $storageCategory = Category::where('name', 'Storage/Industrial')->first();
        $flatCategory = Category::where('name', 'Flat')->first();
        $villaCategory = Category::where('name', 'Villa/Bungalow')->first();
        $plotCategory = Category::where('name', 'Plot')->first();
        $penthouseCategory = Category::where('name', 'Penthouse')->first();

        $subCategories = [];

        // Commercial - Office sub categories
        if ($officeCategory) {
            $subCategories[] = [
                'name' => 'Office Space',
                'category_id' => $officeCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
        }

        // Commercial - Retail sub categories
        if ($retailCategory) {
            $subCategories[] = [
                'name' => 'Ground Floor',
                'category_id' => $retailCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => '1st Floor',
                'category_id' => $retailCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => '2nd Floor',
                'category_id' => $retailCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => '3rd Floor',
                'category_id' => $retailCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
        }

        // Commercial - Storage/Industrial sub categories
        if ($storageCategory) {
            $subCategories[] = [
                'name' => 'Warehouse',
                'category_id' => $storageCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => 'Cold Storage',
                'category_id' => $storageCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => 'IND. Shed',
                'category_id' => $storageCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => 'Plotting',
                'category_id' => $storageCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
        }

        // Residential - Flat sub categories
        if ($flatCategory) {
            $subCategories[] = [
                'name' => '1RK',
                'category_id' => $flatCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => '1BHK',
                'category_id' => $flatCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => '2BHK',
                'category_id' => $flatCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => '3BHK',
                'category_id' => $flatCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => '4BHK',
                'category_id' => $flatCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => '5BHK',
                'category_id' => $flatCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => '5+ BHK',
                'category_id' => $flatCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
        }

        // Residential - Villa/Bungalow sub categories
        if ($villaCategory) {
            $subCategories[] = [
                'name' => '1BHK',
                'category_id' => $villaCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => '2BHK',
                'category_id' => $villaCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => '3BHK',
                'category_id' => $villaCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => '4BHK',
                'category_id' => $villaCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => '4+ BHK',
                'category_id' => $villaCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
        }

        // Residential - Plot (no sub-categories, Plot itself is the item)
        // Note: Plot doesn't have sub-categories according to the image

        // Residential - Penthouse sub categories
        if ($penthouseCategory) {
            $subCategories[] = [
                'name' => '1BHK',
                'category_id' => $penthouseCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => '2BHK',
                'category_id' => $penthouseCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => '3BHK',
                'category_id' => $penthouseCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => '4BHK',
                'category_id' => $penthouseCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => '5BHK',
                'category_id' => $penthouseCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
            $subCategories[] = [
                'name' => '5+ BHK',
                'category_id' => $penthouseCategory->id,
                'is_active' => true,
                'user_id' => 1,
            ];
        }

        foreach ($subCategories as $subCategory) {
            SubCategory::create($subCategory);
        }
    }
}

