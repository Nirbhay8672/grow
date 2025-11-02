<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class ManagementController extends Controller
{
    public function locations()
    {
        $stats = [
            'states' => \App\Models\State::count(),
            'cities' => \App\Models\City::count(),
            'districts' => \App\Models\District::count(),
            'localities' => \App\Models\Locality::count(),
            'talukas' => \App\Models\Taluka::count(),
            'villages' => \App\Models\Village::count(),
        ];

        return Inertia::render('LocationManagement', [
            'stats' => $stats,
        ]);
    }

    public function users()
    {
        $stats = [
            'users' => \App\Models\User::count(),
            'roles' => \Spatie\Permission\Models\Role::count(),
        ];

        return Inertia::render('UserManagement', [
            'stats' => $stats,
        ]);
    }

    public function configuration()
    {
        $stats = [
            'measurementUnits' => \App\Models\MeasurementUnit::count(),
            'builders' => \App\Models\Builder::count(),
        ];

        return Inertia::render('ConfigurationManagement', [
            'stats' => $stats,
        ]);
    }

    public function propertyConfiguration()
    {
        $stats = [
            'constructionTypes' => \App\Models\ConstructionType::count(),
            'categories' => \App\Models\Category::count(),
            'subCategories' => \App\Models\SubCategory::count(),
            'priorityTypes' => \App\Models\PriorityType::count(),
            'propertySources' => \App\Models\PropertySource::count(),
            'ownerTypes' => \App\Models\OwnerType::count(),
            'furnitureTypes' => \App\Models\FurnitureType::count(),
            'propertyZones' => \App\Models\PropertyZone::count(),
            'amenities' => \App\Models\Amenity::count(),
            'propertyConstructionDocuments' => \App\Models\PropertyConstructionDocument::count(),
        ];

        return Inertia::render('PropertyConfigurationManagement', [
            'stats' => $stats,
        ]);
    }
}

