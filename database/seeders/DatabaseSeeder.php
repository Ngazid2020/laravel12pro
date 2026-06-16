<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Commande complète après migrate:fresh :
        //   php artisan migrate:fresh --seed
        // Cela crée : rôles (super_admin, admin), permissions Shield, user admin@reseau.km
        $this->call([
            ShieldSeeder::class,
            SuperAdminSeeder::class,
            TestMemberSeeder::class,
            DemoSeeder::class,
        ]);
    }
}
