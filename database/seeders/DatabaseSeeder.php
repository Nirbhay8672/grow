<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
            StateSeeder::class,
            CitySeeder::class,
            DistrictSeeder::class,
            TalukaSeeder::class,
            LocalitySeeder::class,
            VillageSeeder::class,
            MeasurementUnitSeeder::class,
            BuilderSeeder::class,
            ConstructionTypeSeeder::class,
            CategorySeeder::class,
            SubCategorySeeder::class,
            PriorityTypeSeeder::class,
            PropertySourceSeeder::class,
            OwnerTypeSeeder::class,
            FurnitureTypeSeeder::class,
            PropertyZoneSeeder::class,
            AmenitySeeder::class,
            PropertyConstructionDocumentSeeder::class,
        ]);
    }
}
