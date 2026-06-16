<?php

namespace Database\Seeders;

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Crée les rôles (les permissions seront générées par shield:generate ci-dessous)
        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"web","permissions":[]},{"name":"admin","guard_name":"web","permissions":[]}]';

        $directPermissions = '[]';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        // Génère tous les enregistrements de permissions (Resources, Pages, Widgets)
        // et les assigne aux rôles selon la config Shield
        Artisan::call('shield:generate', [
            '--all'   => true,
            '--panel' => 'admin',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            $superAdminName = Utils::getSuperAdminName();
            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = Role::firstOrCreate(
                    ['name' => $rolePlusPermission['name'], 'guard_name' => $rolePlusPermission['guard_name']]
                );
                if ($rolePlusPermission['name'] !== $superAdminName) {
                    $role->syncPermissions($rolePlusPermission['permissions'] ?? []);
                }
            }
        }
    }

    protected static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            foreach ($permissions as $permission) {
                \Spatie\Permission\Models\Permission::firstOrCreate([
                    'name'       => $permission['name'],
                    'guard_name' => $permission['guard_name'],
                ]);
            }
        }
    }
}
