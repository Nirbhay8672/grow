<?php

namespace App\Http\Controllers;

use App\Http\Requests\MeasurementUnitStoreRequest;
use App\Http\Requests\MeasurementUnitUpdateRequest;
use App\Models\MeasurementUnit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class MeasurementUnitController extends Controller
{
    public function index(Request $request): Response
    {
        $measurementUnits = MeasurementUnit::query()
            ->with('user')
            ->orderBy('name')
            ->get();

        return response($measurementUnits);
    }

    public function store(MeasurementUnitStoreRequest $request): Response
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();

        $measurementUnit = MeasurementUnit::create($validated);
        $measurementUnit->load('user');

        return response($measurementUnit, 201);
    }

    public function show(MeasurementUnit $measurementUnit): Response
    {
        $measurementUnit->load('user');
        return response($measurementUnit);
    }

    public function update(MeasurementUnitUpdateRequest $request, MeasurementUnit $measurementUnit): Response
    {
        $validated = $request->validated();

        $measurementUnit->update($validated);
        $measurementUnit->load('user');

        return response($measurementUnit);
    }

    public function destroy(MeasurementUnit $measurementUnit): Response
    {
        $measurementUnit->delete();

        return response(null, 204);
    }

    public function showPage()
    {
        $measurementUnits = MeasurementUnit::with('user')
            ->orderBy('name')
            ->get();
        
        return Inertia::render('measurement-units/MeasurementUnits', [
            'measurementUnits' => $measurementUnits,
        ]);
    }
}

