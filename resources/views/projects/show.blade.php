<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $project->title }}</h1>
                <p class="text-sm text-gray-500">{{ $project->code }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-outline">
                    <i class="ri-pencil-line"></i> Edit
                </a>
                <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline">
                    <i class="ri-arrow-left-line"></i> Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="container">
        <!-- Project Overview Card -->
        <div class="card mb-6">
            <div class="card-header">
                <h2 class="text-xl font-semibold">Project Overview</h2>
            </div>
            <div class="card-body grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Project Info -->
                <div class="space-y-4">
                    <h3 class="font-medium text-gray-700">Details</h3>
                    <div class="space-y-2">
                        <p class="flex items-center gap-2">
                            <i class="ri-calendar-line text-primary"></i>
                            <span class="text-sm">
                                {{ $project->start_date->format('M d, Y') }} - {{ $project->end_date->format('M d, Y') }}
                            </span>
                        </p>
                        <p class="flex items-center gap-2">
                            <i class="ri-time-line text-primary"></i>
                            <span class="text-sm">
                                {{ $project->start_date->diffInDays($project->end_date) }} days total · 
                                @if(now()->lt($project->end_date))
                                    {{ now()->diffInDays($project->end_date) }} days left
                                @else
                                    Ended {{ $project->end_date->diffForHumans() }}
                                @endif
                            </span>
                        </p>
                        <div class="flex items-center gap-2">
                            <i class="ri-flag-2-line text-primary"></i>
                            <span class="text-sm">
                                Priority: 
                                <span class="badge {{ 
                                    $project->priority === 'urgent' ? 'bg-red-500' : 
                                    ($project->priority === 'high' ? 'bg-orange-500' : 
                                    ($project->priority === 'medium' ? 'bg-yellow-500' : 'bg-gray-500')) 
                                }}">
                                    {{ ucfirst($project->priority) }}
                                </span>
                            </span>
                        </div>
                        @if($project->budget)
                        <p class="flex items-center gap-2">
                            <i class="ri-money-dollar-circle-line text-primary"></i>
                            <span class="text-sm">Budget: ${{ number_format($project->budget, 2) }}</span>
                        </p>
                        @endif
                    </div>
                </div>

                <!-- Progress -->
                <div>
                    <h3 class="font-medium text-gray-700 mb-4">Progress</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span>Completion</span>
                            <span class="font-medium">{{ $project->progress ?? 0 }}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $project->progress ?? 0 }}%;"></div>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <span class="status-badge {{ 
                                $project->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                ($project->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                                ($project->status === 'on_hold' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) 
                            }}">
                                {{ str_replace('_', ' ', ucfirst($project->status)) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div>
                    <h3 class="font-medium text-gray-700 mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-2 gap-2">
                        <a href="#tasks" class="btn btn-sm btn-outline w-full">
                            <i class="ri-task-line"></i> Tasks
                        </a>
                        <a href="#team" class="btn btn-sm btn-outline w-full">
                            <i class="ri-team-line"></i> Team
                        </a>
                        <a href="#files" class="btn btn-sm btn-outline w-full">
                            <i class="ri-folder-2-line"></i> Files
                        </a>
                        <a href="#reports" class="btn btn-sm btn-outline w-full">
                            <i class="ri-bar-chart-2-line"></i> Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Description -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-xl font-semibold">Description</h2>
                    </div>
                    <div class="card-body">
                        <p class="text-gray-700">
                            {{ $project->description ?? 'No description provided.' }}
                        </p>
                    </div>
                </div>

                <!-- Tasks -->
                <div class="card" id="tasks">
                    <div class="card-header flex justify-between items-center">
                        <h2 class="text-xl font-semibold">Tasks</h2>
                        <button class="btn btn-sm btn-primary">
                            <i class="ri-add-line"></i> New Task
                        </button>
                    </div>
                    <div class="card-body p-0">
                        @if($project->tasks->isEmpty())
                            <div class="p-6 text-center text-gray-500">
                                <i class="ri-task-line text-4xl mb-2 text-gray-300"></i>
                                <p>No tasks found for this project.</p>
                            </div>
                        @else
                            <div class="divide-y">
                                @foreach($project->tasks as $task)
                                    <div class="p-4 hover:bg-gray-50 transition-colors">
                                        <div class="flex items-start gap-3">
                                            <input type="checkbox" class="mt-1" 
                                                {{ $task->status === 'completed' ? 'checked' : '' }}>
                                            <div class="flex-1">
                                                <div class="flex justify-between">
                                                    <h4 class="font-medium">{{ $task->title }}</h4>
                                                    <span class="text-xs text-gray-500">
                                                        {{ $task->due_date ? $task->due_date->format('M d') : 'No due date' }}
                                                    </span>
                                                </div>
                                                @if($task->description)
                                                    <p class="text-sm text-gray-600 mt-1">
                                                        {{ Str::limit($task->description, 100) }}
                                                    </p>
                                                @endif
                                                <div class="flex items-center gap-2 mt-2">
                                                    @if($task->assignee)
                                                        <span class="text-xs bg-gray-100 px-2 py-1 rounded-full">
                                                            {{ $task->assignee->name }}
                                                        </span>
                                                    @endif
                                                    <span class="text-xs px-2 py-1 rounded-full {{ 
                                                        $task->priority === 'high' ? 'bg-red-100 text-red-800' :
                                                        ($task->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')
                                                    }}">
                                                        {{ ucfirst($task->priority) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Team Members -->
                <div class="card" id="team">
                    <div class="card-header">
                        <h2 class="text-xl font-semibold">Team Members</h2>
                    </div>
                    <div class="card-body p-0">
                        @if($project->teamMembers->isEmpty())
                            <div class="p-6 text-center text-gray-500">
                                <i class="ri-team-line text-4xl mb-2 text-gray-300"></i>
                                <p>No team members assigned.</p>
                            </div>
                        @else
                            <ul class="divide-y">
                                @foreach($project->teamMembers as $member)
                                    <li class="p-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-600">
                                                {{ substr($member->name, 0, 1) }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="font-medium truncate">{{ $member->name }}</p>
                                                <p class="text-sm text-gray-500 truncate">{{ $member->email }}</p>
                                            </div>
                                            @if($project->owner_id === $member->id)
                                                <span class="badge bg-primary-100 text-primary-800 text-xs">Owner</span>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                <!-- Project Files -->
                <div class="card" id="files">
                    <div class="card-header flex justify-between items-center">
                        <h2 class="text-xl font-semibold">Files</h2>
                        <button class="btn btn-sm btn-outline">
                            <i class="ri-upload-2-line"></i> Upload
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="p-4 text-center text-gray-500">
                            <i class="ri-folder-2-line text-4xl mb-2 text-gray-300"></i>
                            <p>No files uploaded yet.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }
        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }
        .card-body {
            padding: 1.5rem;
        }
        .progress {
            height: 0.5rem;
            background-color: #e5e7eb;
            border-radius: 0.25rem;
            overflow: hidden;
        }
        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #4f46e5 0%, #06b6d4 100%);
            transition: width 0.3s ease;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            line-height: 1;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            transition: all 0.15s ease;
        }
        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
        }
        .btn-primary {
            background-color: #4f46e5;
            color: white;
            border: 1px solid transparent;
        }
        .btn-primary:hover {
            background-color: #4338ca;
        }
        .btn-outline {
            background-color: white;
            border: 1px solid #d1d5db;
            color: #374151;
        }
        .btn-outline:hover {
            background-color: #f9fafb;
            border-color: #9ca3af;
        }
    </style>
</x-app-layout>
