<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1>{{ $project->title }}</h1>
            <div class="btn-group">
                <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Projects
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="card-title">Project Details</h5>
                            <p class="card-text">{{ $project->description ?? 'No description provided.' }}</p>
                            
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <p><strong>Status:</strong> 
                                        <span class="badge bg-{{ 
                                            $project->status === 'completed' ? 'success' : 
                                            ($project->status === 'in_progress' ? 'primary' : 
                                            ($project->status === 'on_hold' ? 'warning' : 'secondary')) 
                                        }}">
                                            {{ str_replace('_', ' ', ucfirst($project->status)) }}
                                        </span>
                                    </p>
                                    <p><strong>Start Date:</strong> {{ $project->start_date->format('M d, Y') }}</p>
                                    <p><strong>End Date:</strong> {{ $project->end_date->format('M d, Y') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Duration:</strong> {{ $project->start_date->diffInDays($project->end_date) }} days</p>
                                    <p><strong>Days Remaining:</strong> 
                                        @if(now()->lt($project->end_date))
                                            {{ now()->diffInDays($project->end_date) }} days left
                                        @else
                                            Project ended {{ $project->end_date->diffForHumans() }}
                                        @endif
                                    </p>
                                    <p><strong>Progress:</strong>
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" style="width: {{ $project->progress ?? 0 }}%" 
                                                 aria-valuenow="{{ $project->progress ?? 0 }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ $project->progress ?? 0 }}%
                                            </div>
                                        </div>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <h5 class="card-title">Team Members</h5>
                            @if($project->teamMembers->isEmpty())
                                <p class="text-muted">No team members assigned.</p>
                            @else
                                <ul class="list-group list-group-flush">
                                    @foreach($project->teamMembers as $member)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-0">{{ $member->name }}</h6>
                                                <small class="text-muted">{{ $member->email }}</small>
                                            </div>
                                            @if($project->owner_id === $member->id)
                                                <span class="badge bg-primary">Owner</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Tasks</h5>
                        <a href="#" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Add Task
                        </a>
                    </div>
                    
                    @if($project->tasks->isEmpty())
                        <div class="text-center py-4">
                            <p class="text-muted">No tasks found for this project.</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($project->tasks as $task)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $task->title }}</h6>
                                            <p class="mb-1 small text-muted">
                                                Assigned to: {{ $task->assignedTo->name ?? 'Unassigned' }}
                                            </p>
                                        </div>
                                        <span class="badge bg-{{ 
                                            $task->status === 'completed' ? 'success' : 
                                            ($task->status === 'in_progress' ? 'primary' : 'secondary')
                                        }}">
                                            {{ str_replace('_', ' ', ucfirst($task->status)) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
