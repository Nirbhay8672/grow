<?php

namespace App\Http\Controllers;

use App\Http\Requests\CityStoreRequest;
use App\Http\Requests\CityUpdateRequest;
use App\Models\City;
use App\Models\State;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class CityController extends Controller
{
    public function index(Request $request): Response
    {
        $cities = City::query()
            ->with('state')
            ->orderBy('name')
            ->get();

        $states = State::where('is_active', true)
            ->orderBy('name')
            ->get();

        return response([
            'cities' => $cities,
            'states' => $states,
        ]);
    }

    public function store(CityStoreRequest $request): Response
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();

        $city = City::create($validated);
        $city->load('state');

        return response($city, 201);
    }

    public function show(City $city): Response
    {
        $city->load('state');
        return response($city);
    }

    public function update(CityUpdateRequest $request, City $city): Response
    {
        $validated = $request->validated();

        $city->update($validated);
        $city->load('state');

        return response($city);
    }

    public function destroy(City $city): Response
    {
        $city->delete();

        return response(null, 204);
    }

    public function getByState(Request $request, State $state): Response
    {
        $cities = City::where('state_id', $state->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response($cities);
    }

    public function showPage()
    {
        $cities = City::with('state')->orderBy('name')->get();
        $states = State::where('is_active', true)->orderBy('name')->get();
        
        return Inertia::render('cities/Cities', [
            'cities' => $cities,
            'states' => $states,
        ]);
    }
}