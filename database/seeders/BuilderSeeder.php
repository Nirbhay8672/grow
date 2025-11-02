<?php

namespace Database\Seeders;

use App\Models\Builder;
use App\Models\User;
use Illuminate\Database\Seeder;

class BuilderSeeder extends Seeder
{
    public function run(): void
    {
        $builders = [
            'ABC Construction',
            'XYZ Builders',
            'Premier Developers',
            'Elite Construction',
            'Modern Builders',
            'ProBuild Construction',
            'Skyline Developers',
            'Golden Gate Builders',
            'Urban Construction',
            'Master Builders',
        ];

        // Get the first user (should exist after UserSeeder runs)
        $user = User::first();
        
        if (!$user) {
            // If no user exists, skip seeding
            $this->command->warn('No users found. Please run UserSeeder first.');
            return;
        }

        foreach ($builders as $builderName) {
            Builder::firstOrCreate(
                ['name' => $builderName],
                [
                    'name' => $builderName,
                    'is_active' => true,
                    'user_id' => $user->id,
                ]
            );
        }
    }
}

