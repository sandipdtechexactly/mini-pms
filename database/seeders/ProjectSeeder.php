<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $managers = User::role('manager')->get();
        $developers = User::role('developer')->get();
        $statuses = ['planning', 'in_progress', 'on_hold', 'completed', 'cancelled'];
        $priorities = ['low', 'medium', 'high', 'urgent'];

        // Create 10 projects
        for ($i = 1; $i <= 10; $i++) {
            $startDate = now()->subDays(rand(5, 60));
            $endDate = (clone $startDate)->addDays(rand(30, 180));
            
            $project = Project::create([
                'name' => 'Project ' . Str::title(Str::random(10)),
                'code' => 'PRJ-' . strtoupper(Str::random(6)),
                'description' => 'This is a test project #' . $i . ' with a detailed description about its goals and objectives.',
                'status' => $statuses[array_rand($statuses)],
                'priority' => $priorities[array_rand($priorities)],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'budget' => rand(5000, 100000),
                'owner_id' => $managers->random()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Attach team members (2-5 developers per project)
            $teamSize = rand(2, min(5, $developers->count()));
            $teamMembers = $developers->random($teamSize);
            $project->teamMembers()->attach($teamMembers);

            $this->command->info("Created project: {$project->name} with {$teamSize} team members");
        }

        $this->command->info('Test projects created successfully!');
    }
}
