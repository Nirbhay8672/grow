<?php

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\seed;

uses(RefreshDatabase::class);

it('creates a role and syncs permissions', function () {
    seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->givePermissionTo('role.create');

    $permissions = ['user.create', 'user.read'];

    actingAs($user)
        ->postJson('/roles', [
            'name' => 'editor',
            'permissions' => $permissions,
        ])
        ->assertCreated()
        ->assertJsonFragment(['name' => 'editor']);
});

it('updates a role name and syncs permissions', function () {
    seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create();
    $user->givePermissionTo(['role.update', 'role.create']);

    $create = $this->postJson('/roles', [
        'name' => 'staff',
        'permissions' => ['user.read'],
    ])->json();

    actingAs($user)
        ->putJson('/roles/'.$create['id'], [
            'name' => 'staff-2',
            'permissions' => ['user.create', 'user.update'],
        ])
        ->assertOk()
        ->assertJsonFragment(['name' => 'staff-2']);
});


