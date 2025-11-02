<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\State;
use App\Models\City;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Get state and city for user (using Maharashtra and Mumbai as example)
        $state = State::where('name', 'Maharashtra')->first();
        $city = $state ? City::where('name', 'Mumbai')->where('state_id', $state->id)->first() : null;

        $user = User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'first_name' => 'Super',
                'middle_name' => 'Master',
                'last_name' => 'Admin',
                'mobile' => '9876543210',
                'email' => 'superadmin@gmail.com',
                'company_name' => 'Grow Technologies',
                'birth_date' => '1990-01-01',
                'state_id' => $state?->id,
                'city_id' => $city?->id,
                'password' => bcrypt('123123'),
                'email_verified_at' => now(),
                'is_active' => true,
            ],
        );

        // Ensure the role exists (seeder creates it) then assign to user
        $user->assignRole('super_admin');

        $user = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'username' => 'admin',
                'first_name' => 'Admin',
                'middle_name' => 'Master',
                'last_name' => 'Admin',
                'mobile' => '9876543210',
                'email' => 'admin@gmail.com',
                'company_name' => 'Grow Technologies',
                'birth_date' => '1990-10-05',
                'state_id' => $state?->id,
                'city_id' => $city?->id,
                'password' => bcrypt('123123'),
                'email_verified_at' => now(),
                'is_active' => true,
            ],
        );

        $user->assignRole('admin');
    }
}

