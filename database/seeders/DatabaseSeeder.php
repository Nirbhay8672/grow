<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'password' => bcrypt('123123'),
                'email_verified_at' => now(),
                'is_active' => true,
            ],
        );

        $this->call([
            RolesAndPermissionsSeeder::class,
            StateSeeder::class,
            CitySeeder::class,
        ]);

        // Ensure the role exists (seeder creates it) then assign to user
        $user->assignRole('super_admin');
    }
}
