<?php

namespace App\Http\Controllers;

use App\Http\Requests\TalukaStoreRequest;
use App\Http\Requests\TalukaUpdateRequest;
use App\Models\District;
use App\Models\Taluka;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class TalukaController extends Controller
{
    public function index(Request $request): Response
    {
        $talukas = Taluka::query()
            ->with('district')
            ->orderBy('name')
            ->get();

        $districts = District::where('is_active', true)
            ->orderBy('name')
            ->get();

        return response([
            'talukas' => $talukas,
            'districts' => $districts,
        ]);
    }

    public function store(TalukaStoreRequest $request): Response
    {
        $validated = $request->validated();

        $taluka = Taluka::create($validated);
        $taluka->load('district');

        return response($taluka, 201);
    }

    public function show(Taluka $taluka): Response
    {
        $taluka->load('district');
        return response($taluka);
    }

    public function update(TalukaUpdateRequest $request, Taluka $taluka): Response
    {
        $validated = $request->validated();

        $taluka->update($validated);
        $taluka->load('district');

        return response($taluka);
    }

    public function destroy(Taluka $taluka): Response
    {
        $taluka->delete();

        return response(null, 204);
    }

    public function getByDistrict(Request $request, District $district): Response
    {
        $talukas = Taluka::where('district_id', $district->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response($talukas);
    }

    public function showPage()
    {
        $talukas = Taluka::with('district')->orderBy('name')->get();
        $districts = District::where('is_active', true)->orderBy('name')->get();
        
        return Inertia::render('talukas/Talukas', [
            'talukas' => $talukas,
            'districts' => $districts,
        ]);
    }
}

