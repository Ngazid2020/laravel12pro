<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@reseau.km'],
            [
                'name'       => 'Super Admin',
                'first_name' => 'Admin',
                'last_name'  => 'Réseau',
                'password'   => Hash::make('Admin@2026!'),
            ]
        );

        $admin->assignRole('super_admin');
    }
}
