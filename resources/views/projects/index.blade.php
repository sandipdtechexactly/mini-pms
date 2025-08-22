@extends('layouts.app')

@section('content')
<h1>Projects</h1>
<p><a href="{{ route('projects.create') }}" role="button" class="btn btn-sm btn-primary"><i class="ri-add-line icon"></i> New Project</a></p>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($projects->isEmpty())
        <article>No projects found. Create your first project to get started.</article>
    @else
        <div class="table-wrap">
            <table role="grid" class="premium compact">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Owner</th>
                        <th>Members</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($projects as $project)
                        <tr>
                            <td>
                                <a href="{{ route('projects.show', $project) }}"><i class="ri-folder-3-line icon"></i> {{ $project->name }}</a>
                            </td>
                            <td>{{ $project->code }}</td>
                            <td>
                                <span class="badge {{ $project->status === 'completed' ? 'badge-success' : ($project->status === 'in_progress' ? 'badge-primary' : ($project->status === 'on_hold' ? 'badge-warning' : 'badge-secondary')) }}">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</span>
                            </td>
                            <td>{{ ucfirst($project->priority) }}</td>
                            <td>{{ $project->start_date->format('M d, Y') }}</td>
                            <td>{{ $project->end_date->format('M d, Y') }}</td>
                            <td>{{ $project->owner?->name }}</td>
                            <td>{{ $project->teamMembers->count() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $projects->links() }}
        </div>
    @endif
</div>
@endsection
