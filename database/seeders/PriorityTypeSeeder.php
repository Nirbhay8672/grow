<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PriorityType;

class PriorityTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $priorityTypes = [
            [
                'name' => 'High Priority',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Medium Priority',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Low Priority',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Urgent',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Normal',
                'is_active' => true,
                'user_id' => 1,
            ],
        ];

        foreach ($priorityTypes as $priorityType) {
            PriorityType::create($priorityType);
        }
    }
}

