<?php

namespace App\Http\Controllers;

use App\Http\Requests\AmenityStoreRequest;
use App\Http\Requests\AmenityUpdateRequest;
use App\Models\Amenity;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class AmenityController extends Controller
{
    public function index(Request $request): Response
    {
        $amenities = Amenity::query()
            ->orderBy('name')
            ->get();

        return response($amenities);
    }

    public function store(AmenityStoreRequest $request): Response
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();

        $amenity = Amenity::create($validated);

        return response($amenity, 201);
    }

    public function show(Amenity $amenity): Response
    {
        return response($amenity);
    }

    public function update(AmenityUpdateRequest $request, Amenity $amenity): Response
    {
        $validated = $request->validated();

        $amenity->update($validated);

        return response($amenity);
    }

    public function destroy(Amenity $amenity): Response
    {
        $amenity->delete();

        return response(null, 204);
    }

    public function showPage()
    {
        $amenities = Amenity::orderBy('name')->get();
        
        return Inertia::render('amenities/Amenities', [
            'amenities' => $amenities,
        ]);
    }
}

