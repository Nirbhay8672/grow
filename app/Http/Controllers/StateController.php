<?php

namespace App\Http\Controllers;

use App\Http\Requests\StateStoreRequest;
use App\Http\Requests\StateUpdateRequest;
use App\Models\State;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class StateController extends Controller
{
    public function index(Request $request): Response
    {
        $states = State::query()
            ->orderBy('name')
            ->get();

        return response($states);
    }

    public function store(StateStoreRequest $request): Response
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();

        $state = State::create($validated);

        return response($state, 201);
    }

    public function show(State $state): Response
    {
        return response($state);
    }

    public function update(StateUpdateRequest $request, State $state): Response
    {
        $validated = $request->validated();

        $state->update($validated);

        return response($state);
    }

    public function destroy(State $state): Response
    {
        $state->delete();

        return response(null, 204);
    }

    public function showPage()
    {
        $states = State::orderBy('name')->get();
        
        return Inertia::render('states/States', [
            'states' => $states,
        ]);
    }
}