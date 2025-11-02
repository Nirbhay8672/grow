<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertySourceStoreRequest;
use App\Http\Requests\PropertySourceUpdateRequest;
use App\Models\PropertySource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class PropertySourceController extends Controller
{
    public function index(Request $request): Response
    {
        $propertySources = PropertySource::query()
            ->orderBy('name')
            ->get();

        return response($propertySources);
    }

    public function store(PropertySourceStoreRequest $request): Response
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();

        $propertySource = PropertySource::create($validated);

        return response($propertySource, 201);
    }

    public function show(PropertySource $propertySource): Response
    {
        return response($propertySource);
    }

    public function update(PropertySourceUpdateRequest $request, PropertySource $propertySource): Response
    {
        $validated = $request->validated();

        $propertySource->update($validated);

        return response($propertySource);
    }

    public function destroy(PropertySource $propertySource): Response
    {
        $propertySource->delete();

        return response(null, 204);
    }

    public function showPage()
    {
        $propertySources = PropertySource::orderBy('name')->get();
        
        return Inertia::render('property-sources/PropertySources', [
            'propertySources' => $propertySources,
        ]);
    }
}

