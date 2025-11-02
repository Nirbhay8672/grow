<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'roles' => Role::count(),
            'users' => \App\Models\User::count(),
            'states' => \App\Models\State::count(),
            'cities' => \App\Models\City::count(),
            'districts' => \App\Models\District::count(),
            'localities' => \App\Models\Locality::count(),
            'talukas' => \App\Models\Taluka::count(),
            'villages' => \App\Models\Village::count(),
            'builders' => \App\Models\Builder::count(),
            'measurementUnits' => \App\Models\MeasurementUnit::count(),
        ];
        
        return Inertia::render('Dashboard', [
            'stats' => $stats,
        ]);
    }
}

