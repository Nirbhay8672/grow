<?php

namespace App\Http\Controllers;

use App\Http\Requests\DistrictStoreRequest;
use App\Http\Requests\DistrictUpdateRequest;
use App\Models\District;
use App\Models\State;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class DistrictController extends Controller
{
    public function index(Request $request): Response
    {
        $districts = District::query()
            ->with('state')
            ->orderBy('name')
            ->get();

        $states = State::where('is_active', true)
            ->orderBy('name')
            ->get();

        return response([
            'districts' => $districts,
            'states' => $states,
        ]);
    }

    public function store(DistrictStoreRequest $request): Response
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();

        $district = District::create($validated);
        $district->load('state');

        return response($district, 201);
    }

    public function show(District $district): Response
    {
        $district->load('state');
        return response($district);
    }

    public function update(DistrictUpdateRequest $request, District $district): Response
    {
        $validated = $request->validated();

        $district->update($validated);
        $district->load('state');

        return response($district);
    }

    public function destroy(District $district): Response
    {
        $district->delete();

        return response(null, 204);
    }

    public function getByState(Request $request, State $state): Response
    {
        $districts = District::where('state_id', $state->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response($districts);
    }

    public function showPage()
    {
        $districts = District::with('state')->orderBy('name')->get();
        $states = State::where('is_active', true)->orderBy('name')->get();
        
        return Inertia::render('districts/Districts', [
            'districts' => $districts,
            'states' => $states,
        ]);
    }
}

