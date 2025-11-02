<?php

namespace Database\Seeders;

use App\Models\MeasurementUnit;
use App\Models\User;
use Illuminate\Database\Seeder;

class MeasurementUnitSeeder extends Seeder
{
    public function run(): void
    {
        $measurementUnits = [
            'Acre',
            'Bigha',
            'Biswa',
            'Biswa Kacha',
            'Cent',
            'Chatak',
            'Decimal',
            'Dhur',
            'Ft.',
            'Gaj',
        ];

        // Get the first user or create a default user for seeding
        $user = User::first();
        
        foreach ($measurementUnits as $unitName) {
            MeasurementUnit::create([
                'name' => $unitName,
                'is_active' => true,
                'user_id' => $user->id,
            ]);
        }
    }
}

