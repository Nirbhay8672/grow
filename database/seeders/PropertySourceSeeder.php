<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PropertySource;

class PropertySourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $propertySources = [
            [
                'name' => 'Direct Owner',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Real Estate Agent',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Builder',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Property Portal',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Referral',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Online Advertisement',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Newspaper Advertisement',
                'is_active' => true,
                'user_id' => 1,
            ],
        ];

        foreach ($propertySources as $propertySource) {
            PropertySource::create($propertySource);
        }
    }
}

