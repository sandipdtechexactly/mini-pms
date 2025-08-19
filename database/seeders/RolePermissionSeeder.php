<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User permissions
            'view users', 'create users', 'edit users', 'delete users',
            // Project permissions
            'view projects', 'create projects', 'edit projects', 'delete projects', 'manage project members',
            // Task permissions
            'view tasks', 'create tasks', 'edit tasks', 'delete tasks', 'assign tasks', 'change task status',
            // Role & Permission permissions
            'view roles', 'create roles', 'edit roles', 'delete roles',
            'view permissions', 'assign permissions', 'revoke permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign created permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $managerPermissions = [
            'view users', 'create users', 'edit users',
            'view projects', 'create projects', 'edit projects', 'manage project members',
            'view tasks', 'create tasks', 'edit tasks', 'assign tasks', 'change task status',
        ];
        $managerRole->syncPermissions($managerPermissions);

        $developerRole = Role::firstOrCreate(['name' => 'developer', 'guard_name' => 'web']);
        $developerPermissions = [
            'view projects', 'view tasks', 'create tasks', 'edit tasks', 'change task status',
        ];
        $developerRole->syncPermissions($developerPermissions);

        $this->command->info('Roles and permissions seeded successfully!');
    }
}
