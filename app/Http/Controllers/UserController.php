<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $users = User::query()
            ->with(['state', 'city'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response($users);
    }

    public function store(UserStoreRequest $request): Response
    {
        $validated = $request->validated();
        
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user = User::create($validated);
        $user->load(['state', 'city']);

        return response($user, 201);
    }

    public function show(User $user): Response
    {
        $user->load(['state', 'city', 'roles']);
        return response($user);
    }

    public function update(UserUpdateRequest $request, User $user): Response
    {
        $validated = $request->validated();
        
        if (isset($validated['password']) && !empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        $user->load(['state', 'city']);

        return response($user);
    }

    public function destroy(User $user): Response
    {
        $user->delete();

        return response(null, 204);
    }

    public function toggleActive(User $user): Response
    {
        $user->update(['is_active' => !$user->is_active]);
        $user->load(['state', 'city']);

        return response($user);
    }

    public function showPage()
    {
        $users = User::with(['state', 'city', 'roles'])->orderBy('created_at', 'desc')->get();
        $states = State::where('is_active', true)->orderBy('name')->get();
        
        return Inertia::render('users/Users', [
            'users' => $users,
            'states' => $states,
        ]);
    }

    public function showProfile()
    {
        $user = Auth::user();
        /** @var \App\Models\User|null $user */
        $user?->load(['state', 'city']);
        $states = State::where('is_active', true)->orderBy('name')->get();
        
        return Inertia::render('Profile', [
            'user' => $user,
            'states' => $states,
        ]);
    }

    public function updateProfile(Request $request): Response
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'username' => 'sometimes|required|string|max:255|unique:users,username,' . $user->id,
            'first_name' => 'sometimes|required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'mobile' => 'nullable|string|max:20',
            'email' => 'sometimes|required|email|max:255|unique:users,email,' . $user->id,
            'company_name' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        
        if (isset($validated['password']) && !empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        $user->load(['state', 'city']);

        return response($user);
    }
}