<?php

namespace Database\Seeders;

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"web","permissions":["view_user","view_any_user","create_user","update_user","restore_user","restore_any_user","replicate_user","reorder_user","delete_user","delete_any_user","force_delete_user","force_delete_any_user","view_member::profile","view_any_member::profile","create_member::profile","update_member::profile","restore_member::profile","restore_any_member::profile","replicate_member::profile","reorder_member::profile","delete_member::profile","delete_any_member::profile","force_delete_member::profile","force_delete_any_member::profile","view_candidature::application","view_any_candidature::application","create_candidature::application","update_candidature::application","delete_candidature::application","delete_any_candidature::application","view_payment","view_any_payment","create_payment","update_payment","delete_payment","delete_any_payment","view_subscription::plan","view_any_subscription::plan","create_subscription::plan","update_subscription::plan","delete_subscription::plan","delete_any_subscription::plan","view_partner::company","view_any_partner::company","create_partner::company","update_partner::company","delete_partner::company","delete_any_partner::company","view_recommendation","view_any_recommendation","create_recommendation","update_recommendation","delete_recommendation","delete_any_recommendation","view_training","view_any_training","create_training","update_training","delete_training","delete_any_training","view_opportunity","view_any_opportunity","create_opportunity","update_opportunity","delete_opportunity","delete_any_opportunity","view_event","view_any_event","create_event","update_event","delete_event","delete_any_event","view_level","view_any_level","create_level","update_level","delete_level","delete_any_level","view_announcement","view_any_announcement","create_announcement","update_announcement","delete_announcement","delete_any_announcement","page_HealthCheckResults","widget_StatsOverview","widget_AccountWidget"]}]';

        $directPermissions = '[]';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);
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
