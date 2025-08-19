<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Clear existing data first
            // Note: Be careful with this in production
            // DatabaseTruncateSeeder::class,
            
            // Seed roles and permissions first
            RolePermissionSeeder::class,
            
            // Then seed users (depends on roles)
            UserSeeder::class,
            
            // Then seed projects (depends on users)
            ProjectSeeder::class,
            
            // Finally seed tasks (depends on projects and users)
            TaskSeeder::class,
        ]);
        
        $this->command->info('All seeders completed successfully!');
    }
}
