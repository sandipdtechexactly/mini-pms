@extends('layouts.app')

@section('content')


@if (session('status'))
    <article class="success">{{ session('status') }}</article>
@endif

<div class="table-wrap">
<table role="grid" class="premium compact">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Project</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Due Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tasks as $task)
                                    <tr>
                                        <td>{{ $task->title }}</td>
                                        <td>{{ $task->project->name }}</td>
                                        <td><span class="badge {{ $task->status === 'completed' ? 'badge-success' : ($task->status === 'in_progress' ? 'badge-primary' : 'badge-warning') }}">{{ ucfirst(str_replace('_', ' ', $task->status)) }}</span></td>
                                        <td>{{ ucfirst($task->priority) }}</td>
                                        <td>{{ optional($task->due_date)->format('M d, Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">No tasks assigned to you.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
</div>

{{ $tasks->links() }}
@endsection
