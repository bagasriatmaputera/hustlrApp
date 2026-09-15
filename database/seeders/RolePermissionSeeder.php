<?php

namespace Database\Seeders;

use App\PermissionManager\IntegratedPersonalSystem\ManagementPermissionManager;
use App\PermissionManager\IntegratedPersonalSystem\OwnerPermissionManager;
use App\PermissionManager\IntegratedPersonalSystem\StaffPermissionManager;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    protected array $managers = [
        OwnerPermissionManager::class,
        ManagementPermissionManager::class,
        StaffPermissionManager::class,
    ];

    public function run(): void
    {
        // Sync Cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        foreach ($this->managers as $managerClass) {
            $manager = new $managerClass;

            foreach ($manager->getPermissions() as $permissionName) {
                Permission::firstOrCreate(['name' => $permissionName]);
            }

            $role = Role::firstOrCreate(['name' => $manager->getRole()]);
            $role->syncPermissions($manager->getPermissions());
        }

        $this->command->info('Roles & permissions seeded successfully.');
    }
}
