<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocalityStoreRequest;
use App\Http\Requests\LocalityUpdateRequest;
use App\Models\City;
use App\Models\Locality;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class LocalityController extends Controller
{
    public function index(Request $request): Response
    {
        $localities = Locality::query()
            ->with(['state', 'city'])
            ->orderBy('name')
            ->get();

        $states = State::where('is_active', true)
            ->orderBy('name')
            ->get();

        return response([
            'localities' => $localities,
            'states' => $states,
        ]);
    }

    public function store(LocalityStoreRequest $request): Response
    {
        $validated = $request->validated();

        $locality = Locality::create($validated);
        $locality->load(['state', 'city']);

        return response($locality, 201);
    }

    public function show(Locality $locality): Response
    {
        $locality->load(['state', 'city']);
        return response($locality);
    }

    public function update(LocalityUpdateRequest $request, Locality $locality): Response
    {
        $validated = $request->validated();

        $locality->update($validated);
        $locality->load(['state', 'city']);

        return response($locality);
    }

    public function destroy(Locality $locality): Response
    {
        $locality->delete();

        return response(null, 204);
    }

    public function getByCity(Request $request, City $city): Response
    {
        $localities = Locality::where('city_id', $city->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response($localities);
    }

    public function showPage()
    {
        $localities = Locality::with(['state', 'city'])->orderBy('name')->get();
        $states = State::where('is_active', true)->orderBy('name')->get();
        
        return Inertia::render('localities/Localities', [
            'localities' => $localities,
            'states' => $states,
        ]);
    }
}

