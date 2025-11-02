<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConstructionTypeStoreRequest;
use App\Http\Requests\ConstructionTypeUpdateRequest;
use App\Models\ConstructionType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class ConstructionTypeController extends Controller
{
    public function index(Request $request): Response
    {
        $constructionTypes = ConstructionType::query()
            ->orderBy('name')
            ->get();

        return response($constructionTypes);
    }

    public function store(ConstructionTypeStoreRequest $request): Response
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();

        $constructionType = ConstructionType::create($validated);

        return response($constructionType, 201);
    }

    public function show(ConstructionType $constructionType): Response
    {
        return response($constructionType);
    }

    public function update(ConstructionTypeUpdateRequest $request, ConstructionType $constructionType): Response
    {
        $validated = $request->validated();

        $constructionType->update($validated);

        return response($constructionType);
    }

    public function destroy(ConstructionType $constructionType): Response
    {
        $constructionType->delete();

        return response(null, 204);
    }

    public function showPage()
    {
        $constructionTypes = ConstructionType::orderBy('name')
            ->get();
        
        return Inertia::render('construction-types/ConstructionTypes', [
            'constructionTypes' => $constructionTypes,
        ]);
    }
}

