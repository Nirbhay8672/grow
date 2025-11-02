<?php

namespace Database\Seeders;

use App\Models\MeasurementUnit;
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

        foreach ($measurementUnits as $unitName) {
            MeasurementUnit::create([
                'name' => $unitName,
                'is_active' => true,
                'user_id' => 1,
            ]);
        }
    }
}

