<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyZoneStoreRequest;
use App\Http\Requests\PropertyZoneUpdateRequest;
use App\Models\PropertyZone;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class PropertyZoneController extends Controller
{
    public function index(Request $request): Response
    {
        $propertyZones = PropertyZone::query()
            ->orderBy('name')
            ->get();

        return response($propertyZones);
    }

    public function store(PropertyZoneStoreRequest $request): Response
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();

        $propertyZone = PropertyZone::create($validated);

        return response($propertyZone, 201);
    }

    public function show(PropertyZone $propertyZone): Response
    {
        return response($propertyZone);
    }

    public function update(PropertyZoneUpdateRequest $request, PropertyZone $propertyZone): Response
    {
        $validated = $request->validated();

        $propertyZone->update($validated);

        return response($propertyZone);
    }

    public function destroy(PropertyZone $propertyZone): Response
    {
        $propertyZone->delete();

        return response(null, 204);
    }

    public function showPage()
    {
        $propertyZones = PropertyZone::orderBy('name')->get();
        
        return Inertia::render('property-zones/PropertyZones', [
            'propertyZones' => $propertyZones,
        ]);
    }
}

