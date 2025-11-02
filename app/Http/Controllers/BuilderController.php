<?php

namespace App\Http\Controllers;

use App\Http\Requests\BuilderStoreRequest;
use App\Http\Requests\BuilderUpdateRequest;
use App\Models\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class BuilderController extends Controller
{
    public function index(Request $request): Response
    {
        $builders = Builder::query()
            ->orderBy('name')
            ->get();

        return response($builders);
    }

    public function store(BuilderStoreRequest $request): Response
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();

        $builder = Builder::create($validated);

        return response($builder, 201);
    }

    public function show(Builder $builder): Response
    {
        return response($builder);
    }

    public function update(BuilderUpdateRequest $request, Builder $builder): Response
    {
        $validated = $request->validated();

        $builder->update($validated);

        return response($builder);
    }

    public function destroy(Builder $builder): Response
    {
        $builder->delete();

        return response(null, 204);
    }

    public function showPage()
    {
        $builders = Builder::orderBy('name')
            ->get();
        
        return Inertia::render('builders/Builders', [
            'builders' => $builders,
        ]);
    }
}

