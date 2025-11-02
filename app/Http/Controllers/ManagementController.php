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
        ];

        return Inertia::render('ConfigurationManagement', [
            'stats' => $stats,
        ]);
    }
}

