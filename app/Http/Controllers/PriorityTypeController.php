<?php

namespace App\Http\Controllers;

use App\Http\Requests\PriorityTypeStoreRequest;
use App\Http\Requests\PriorityTypeUpdateRequest;
use App\Models\PriorityType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class PriorityTypeController extends Controller
{
    public function index(Request $request): Response
    {
        $priorityTypes = PriorityType::query()
            ->orderBy('name')
            ->get();

        return response($priorityTypes);
    }

    public function store(PriorityTypeStoreRequest $request): Response
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();

        $priorityType = PriorityType::create($validated);

        return response($priorityType, 201);
    }

    public function show(PriorityType $priorityType): Response
    {
        return response($priorityType);
    }

    public function update(PriorityTypeUpdateRequest $request, PriorityType $priorityType): Response
    {
        $validated = $request->validated();

        $priorityType->update($validated);

        return response($priorityType);
    }

    public function destroy(PriorityType $priorityType): Response
    {
        $priorityType->delete();

        return response(null, 204);
    }

    public function showPage()
    {
        $priorityTypes = PriorityType::orderBy('name')->get();
        
        return Inertia::render('priority-types/PriorityTypes', [
            'priorityTypes' => $priorityTypes,
        ]);
    }
}

