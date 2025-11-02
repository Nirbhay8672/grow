<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyConstructionDocumentStoreRequest;
use App\Http\Requests\PropertyConstructionDocumentUpdateRequest;
use App\Models\PropertyConstructionDocument;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class PropertyConstructionDocumentController extends Controller
{
    public function index(Request $request): Response
    {
        $propertyConstructionDocuments = PropertyConstructionDocument::query()
            ->orderBy('name')
            ->get();

        return response($propertyConstructionDocuments);
    }

    public function store(PropertyConstructionDocumentStoreRequest $request): Response
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();

        $propertyConstructionDocument = PropertyConstructionDocument::create($validated);

        return response($propertyConstructionDocument, 201);
    }

    public function show(PropertyConstructionDocument $propertyConstructionDocument): Response
    {
        return response($propertyConstructionDocument);
    }

    public function update(PropertyConstructionDocumentUpdateRequest $request, PropertyConstructionDocument $propertyConstructionDocument): Response
    {
        $validated = $request->validated();

        $propertyConstructionDocument->update($validated);

        return response($propertyConstructionDocument);
    }

    public function destroy(PropertyConstructionDocument $propertyConstructionDocument): Response
    {
        $propertyConstructionDocument->delete();

        return response(null, 204);
    }

    public function showPage()
    {
        $propertyConstructionDocuments = PropertyConstructionDocument::orderBy('name')->get();
        
        return Inertia::render('property-construction-documents/PropertyConstructionDocuments', [
            'propertyConstructionDocuments' => $propertyConstructionDocuments,
        ]);
    }
}

