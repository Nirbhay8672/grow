<?php

namespace App\Http\Controllers;

use App\Models\Builder;
use App\Models\City;
use App\Models\Locality;
use App\Models\MeasurementUnit;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function create(): Response
    {
        $builders = Builder::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $states = State::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $measurementUnits = MeasurementUnit::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $users = User::orderBy('name')
            ->get(['id', 'name', 'email']);

        return Inertia::render('projects/CreateProject', [
            'builders' => $builders,
            'states' => $states,
            'measurementUnits' => $measurementUnits,
            'users' => $users,
        ]);
    }

    public function getCitiesByState(Request $request, State $state)
    {
        $cities = City::where('state_id', $state->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($cities);
    }

    public function getLocalitiesByCity(Request $request, City $city)
    {
        $localities = Locality::where('city_id', $city->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'zip_code']);

        return response()->json($localities);
    }
}

