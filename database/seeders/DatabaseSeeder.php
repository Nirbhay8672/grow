<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\State;
use App\Models\City;
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
        ]);
    }
}
