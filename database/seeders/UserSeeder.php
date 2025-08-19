<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure roles exist first
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $developerRole = Role::firstOrCreate(['name' => 'developer', 'guard_name' => 'web']);

        // Create admin user
        $admin = User::firstOrNew(['email' => 'admin@example.com']);
        if (!$admin->exists) {
            $admin->fill([
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ])->save();
        }
        $admin->syncRoles([$adminRole->id]);

        // Create manager users
        for ($i = 1; $i <= 3; $i++) {
            $manager = User::firstOrNew(['email' => "manager{$i}@example.com"]);
            if (!$manager->exists) {
                $manager->fill([
                    'name' => "Manager {$i}",
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'is_active' => true,
                ])->save();
            }
            $manager->syncRoles([$managerRole->id]);
        }

        // Create developer users
        for ($i = 1; $i <= 10; $i++) {
            $developer = User::firstOrNew(['email' => "developer{$i}@example.com"]);
            if (!$developer->exists) {
                $developer->fill([
                    'name' => "Developer {$i}",
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'is_active' => true,
                ])->save();
            }
            $developer->syncRoles([$developerRole->id]);
        }

        $this->command->info('Test users created successfully!');
    }
}
