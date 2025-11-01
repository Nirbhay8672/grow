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
        ];
        
        return Inertia::render('Dashboard', [
            'stats' => $stats,
        ]);
    }
}

