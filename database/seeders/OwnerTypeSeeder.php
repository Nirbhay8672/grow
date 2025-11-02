<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OwnerType;

class OwnerTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ownerTypes = [
            [
                'name' => 'Individual',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Company',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Partnership Firm',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'LLP (Limited Liability Partnership)',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Trust',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Society',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Government',
                'is_active' => true,
                'user_id' => 1,
            ],
        ];

        foreach ($ownerTypes as $ownerType) {
            OwnerType::create($ownerType);
        }
    }
}

