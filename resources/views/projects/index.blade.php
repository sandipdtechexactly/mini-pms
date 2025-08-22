@extends('layouts.app')

@section('content')
<h1>Projects</h1>
<p><a href="{{ route('projects.create') }}" role="button">New Project</a></p>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($projects->isEmpty())
        <article>No projects found. Create your first project to get started.</article>
    @else
        <div>
            <table role="grid">
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
                                <a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a>
                            </td>
                            <td>{{ $project->code }}</td>
                            <td>
                                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
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
