<?php

namespace App\Http\Controllers;

use App\Http\Requests\OwnerTypeStoreRequest;
use App\Http\Requests\OwnerTypeUpdateRequest;
use App\Models\OwnerType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class OwnerTypeController extends Controller
{
    public function index(Request $request): Response
    {
        $ownerTypes = OwnerType::query()
            ->orderBy('name')
            ->get();

        return response($ownerTypes);
    }

    public function store(OwnerTypeStoreRequest $request): Response
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();

        $ownerType = OwnerType::create($validated);

        return response($ownerType, 201);
    }

    public function show(OwnerType $ownerType): Response
    {
        return response($ownerType);
    }

    public function update(OwnerTypeUpdateRequest $request, OwnerType $ownerType): Response
    {
        $validated = $request->validated();

        $ownerType->update($validated);

        return response($ownerType);
    }

    public function destroy(OwnerType $ownerType): Response
    {
        $ownerType->delete();

        return response(null, 204);
    }

    public function showPage()
    {
        $ownerTypes = OwnerType::orderBy('name')->get();
        
        return Inertia::render('owner-types/OwnerTypes', [
            'ownerTypes' => $ownerTypes,
        ]);
    }
}

