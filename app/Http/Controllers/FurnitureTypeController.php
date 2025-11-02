<?php

namespace App\Http\Controllers;

use App\Http\Requests\FurnitureTypeStoreRequest;
use App\Http\Requests\FurnitureTypeUpdateRequest;
use App\Models\FurnitureType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class FurnitureTypeController extends Controller
{
    public function index(Request $request): Response
    {
        $furnitureTypes = FurnitureType::query()
            ->orderBy('name')
            ->get();

        return response($furnitureTypes);
    }

    public function store(FurnitureTypeStoreRequest $request): Response
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();

        $furnitureType = FurnitureType::create($validated);

        return response($furnitureType, 201);
    }

    public function show(FurnitureType $furnitureType): Response
    {
        return response($furnitureType);
    }

    public function update(FurnitureTypeUpdateRequest $request, FurnitureType $furnitureType): Response
    {
        $validated = $request->validated();

        $furnitureType->update($validated);

        return response($furnitureType);
    }

    public function destroy(FurnitureType $furnitureType): Response
    {
        $furnitureType->delete();

        return response(null, 204);
    }

    public function showPage()
    {
        $furnitureTypes = FurnitureType::orderBy('name')->get();
        
        return Inertia::render('furniture-types/FurnitureTypes', [
            'furnitureTypes' => $furnitureTypes,
        ]);
    }
}

