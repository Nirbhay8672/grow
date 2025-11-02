<?php

namespace App\Http\Controllers;

use App\Http\Requests\VillageStoreRequest;
use App\Http\Requests\VillageUpdateRequest;
use App\Models\District;
use App\Models\Taluka;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class VillageController extends Controller
{
    public function index(Request $request): Response
    {
        $villages = Village::query()
            ->with(['district', 'taluka'])
            ->orderBy('name')
            ->get();

        $districts = District::where('is_active', true)
            ->orderBy('name')
            ->get();

        return response([
            'villages' => $villages,
            'districts' => $districts,
        ]);
    }

    public function store(VillageStoreRequest $request): Response
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();

        $village = Village::create($validated);
        $village->load(['district', 'taluka']);

        return response($village, 201);
    }

    public function show(Village $village): Response
    {
        $village->load(['district', 'taluka']);
        return response($village);
    }

    public function update(VillageUpdateRequest $request, Village $village): Response
    {
        $validated = $request->validated();

        $village->update($validated);
        $village->load(['district', 'taluka']);

        return response($village);
    }

    public function destroy(Village $village): Response
    {
        $village->delete();

        return response(null, 204);
    }

    public function getByTaluka(Request $request, Taluka $taluka): Response
    {
        $villages = Village::where('taluka_id', $taluka->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response($villages);
    }

    public function showPage()
    {
        $villages = Village::with(['district', 'taluka'])->orderBy('name')->get();
        $districts = District::where('is_active', true)->orderBy('name')->get();
        
        return Inertia::render('villages/Villages', [
            'villages' => $villages,
            'districts' => $districts,
        ]);
    }
}

