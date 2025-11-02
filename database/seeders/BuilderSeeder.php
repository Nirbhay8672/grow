<?php

namespace Database\Seeders;

use App\Models\Builder;
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

        foreach ($builders as $builderName) {
            Builder::firstOrCreate(
                ['name' => $builderName],
                [
                    'name' => $builderName,
                    'is_active' => true,
                    'user_id' => 1,
                ]
            );
        }
    }
}

