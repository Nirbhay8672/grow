<?php

namespace Database\Seeders;

use App\Models\MeasurementUnit;
use Illuminate\Database\Seeder;

class MeasurementUnitSeeder extends Seeder
{
    public function run(): void
    {
        $measurementUnits = [
            'SqFt',
            'SqYd',
            'SqM',
            'Acre',
            'Bigha',
            'Biswa',
            'Biswa Kacha',
            'Cent',
            'Chatak',
            'Decimal',
            'Dhur',
            'Gaj',
            'Ground',
            'Kanal',
            'Katha',
            'Killa',
            'Lessa',
            'Marla',
            'Murabba',
            'Pura',
            'Sq. Km.',
            'Sq. Karam',
            'Sq. Mile',
            'Ft.',
            'Mt.',
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

