<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleStoreRequest;
use App\Http\Requests\RoleUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request): Response
    {
        $roles = Role::query()
            ->with('permissions')
            ->orderBy('name')
            ->get();

        return response($roles);
    }

    public function store(RoleStoreRequest $request): Response
    {
        $validated = $request->validated();

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        return response($role->load('permissions'), 201);
    }

    public function show(Role $role): Response
    {
        return response($role->load('permissions'));
    }

    public function update(RoleUpdateRequest $request, Role $role): Response
    {
        $validated = $request->validated();

        if (isset($validated['name'])) {
            $role->name = $validated['name'];
            $role->save();
        }

        if (array_key_exists('permissions', $validated)) {
            $role->syncPermissions($validated['permissions'] ?? []);
        }

        return response($role->load('permissions'));
    }

    public function destroy(Role $role): Response
    {
        $role->delete();

        return response(null, 204);
    }
}


