@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            {{ config('app.name') }}
        @endcomponent
    @endslot

    # Task Assigned: {{ $task->title }}

    Hello {{ $assignee->name }},

    You have been assigned a new task by {{ $assignedBy->name }}.

    **Task Details:**
    - **Project:** {{ $task->project->name }}
    - **Title:** {{ $task->title }}
    - **Priority:** {{ ucfirst($task->priority) }}
    - **Due Date:** {{ $task->due_date ? $task->due_date->format('F j, Y') : 'No due date' }}
    - **Status:** {{ ucfirst(str_replace('_', ' ', $task->status)) }}

    @if($task->description)
        **Description:**
        {{ $task->description }}
    @endif

    @component('mail::button', ['url' => $taskUrl])
        View Task
    @endcomponent

    Thanks,<br>
    {{ config('app.name') }}

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        @endcomponent
    @endslot
@endcomponent
