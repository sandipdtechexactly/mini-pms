<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = Project::with('teamMembers')->get();
        $statuses = ['pending', 'in_progress', 'in_review', 'completed', 'blocked'];
        $priorities = ['low', 'medium', 'high', 'critical'];
        $taskTemplates = [
            'Implement {feature} for {project}',
            'Fix bug in {module} of {project}',
            'Write tests for {feature}',
            'Update documentation for {project}',
            'Review and merge pull request for {feature}',
            'Optimize performance of {module}',
            'Add validation for {input} in {module}',
            'Refactor {component} in {project}',
            'Create API endpoint for {feature}',
            'Update UI for {page} page'
        ];

        $taskCount = 0;
        
        foreach ($projects as $project) {
            // Create 5-15 tasks per project
            $taskNumber = rand(5, 15);
            $teamMembers = $project->teamMembers;
            
            for ($i = 1; $i <= $taskNumber; $i++) {
                $status = $statuses[array_rand($statuses)];
                $priority = $priorities[array_rand($priorities)];
                $startDate = $project->start_date->copy()->addDays(rand(0, 30));
                $dueDate = $startDate->copy()->addDays(rand(1, 14));
                
                // Ensure due date doesn't exceed project end date
                if ($dueDate > $project->end_date) {
                    $dueDate = $project->end_date;
                }
                
                // Select a random task template and replace placeholders
                $template = $taskTemplates[array_rand($taskTemplates)];
                $title = str_replace(
                    ['{feature}', '{module}', '{project}', '{input}', '{component}', '{page}'],
                    [
                        ['user authentication', 'payment processing', 'reporting module', 'API integration', 'dashboard', 'notifications'][array_rand([0, 1, 2, 3, 4, 5])],
                        ['user management', 'billing', 'API', 'database', 'frontend', 'backend'][array_rand([0, 1, 2, 3, 4, 5])],
                        $project->name,
                        ['email', 'password', 'file upload', 'form data', 'search query'][array_rand([0, 1, 2, 3, 4])],
                        ['service class', 'repository', 'controller', 'middleware', 'trait'][array_rand([0, 1, 2, 3, 4])],
                        ['dashboard', 'profile', 'settings', 'reports', 'analytics'][array_rand([0, 1, 2, 3, 4])],
                    ],
                    $template
                );
                
                // Create the task
                $task = new Task([
                    'title' => $title,
                    'description' => 'This is a test task for ' . $project->name . '. ' . 
                                    'The task involves ' . strtolower($title) . '. ' .
                                    'Please ensure all requirements are met before marking as complete.',
                    'status' => $status,
                    'priority' => $priority,
                    'due_date' => $dueDate,
                    'estimated_hours' => rand(1, 16),
                    'project_id' => $project->id,
                    'assigned_to' => $teamMembers->isNotEmpty() ? $teamMembers->random()->id : null,
                    'created_by' => $project->owner_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $task->save();
                $taskCount++;
                
                // Randomly add some task dependencies
                if ($i > 1 && rand(1, 3) === 1) {
                    $task->dependencies()->attach(
                        $project->tasks()->inRandomOrder()->first()->id,
                        ['created_at' => now(), 'updated_at' => now()]
                    );
                }
            }
            
            $this->command->info("Created {$taskNumber} tasks for project: {$project->name}");
        }
        
        $this->command->info("Created a total of {$taskCount} test tasks!");
    }
}
