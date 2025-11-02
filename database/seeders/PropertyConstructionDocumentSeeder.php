<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PropertyConstructionDocument;

class PropertyConstructionDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $propertyConstructionDocuments = [
            [
                'name' => 'Building Plan Approval',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Occupancy Certificate',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Completion Certificate',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Fire Safety Certificate',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Structural Stability Certificate',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Environmental Clearance',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'NOC from Water Supply Authority',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'NOC from Electricity Board',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'RERA Registration',
                'is_active' => true,
                'user_id' => 1,
            ],
            [
                'name' => 'Title Deed',
                'is_active' => true,
                'user_id' => 1,
            ],
        ];

        foreach ($propertyConstructionDocuments as $document) {
            PropertyConstructionDocument::create($document);
        }
    }
}

