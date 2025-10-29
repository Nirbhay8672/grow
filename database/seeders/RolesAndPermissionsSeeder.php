<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Define permissions
        $permissions = [
            'role.create',
            'role.read',
            'role.update',
            'role.delete',
            'user.create',
            'user.read',
            'user.update',
            'user.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // Create roles
        $superAdmin = Role::findOrCreate('super_admin', 'web');
        $admin = Role::findOrCreate('admin', 'web');

        // Assign permissions
        $superAdmin->syncPermissions($permissions);
        $admin->syncPermissions([
            'user.create',
            'user.read',
            'user.update',
            'user.delete',
        ]);
    }
}
