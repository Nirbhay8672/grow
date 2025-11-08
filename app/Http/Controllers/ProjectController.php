<?php

namespace App\Http\Controllers;

use App\Models\Builder;
use App\Models\Category;
use App\Models\City;
use App\Models\ConstructionType;
use App\Models\Locality;
use App\Models\MeasurementUnit;
use App\Models\Project;
use App\Models\ProjectContact;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(): Response
    {
        $projects = Project::with(['builder', 'state', 'city', 'locality', 'contacts'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('projects/Projects', [
            'projects' => $projects,
        ]);
    }

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

        $constructionTypes = ConstructionType::where('is_active', true)
            ->with(['categories' => function ($query) {
                $query->where('is_active', true)
                    ->with(['subCategories' => function ($subQuery) {
                        $subQuery->where('is_active', true);
                    }]);
            }])
            ->get(['id', 'name']);

        return Inertia::render('projects/CreateProject', [
            'builders' => $builders,
            'states' => $states,
            'measurementUnits' => $measurementUnits,
            'users' => $users,
            'constructionTypes' => $constructionTypes,
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

