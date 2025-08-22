@extends('layouts.app')

@section('content')
@php
    use App\Models\Project;
    use App\Models\Task;

    $projectsTotal = Project::count();
    $projectsActive = Project::whereIn('status', ['planning','in_progress','on_hold'])->count();

    $taskQuery = Task::query();
    if(auth()->user()?->hasRole('developer')) {
        $taskQuery->where('assigned_to', auth()->id());
    }
    $tasksOpen = (clone $taskQuery)->whereNotIn('status', ['completed'])->count();
    $tasksOverdue = (clone $taskQuery)->whereNotIn('status',["completed"])->whereNotNull('due_date')->whereDate('due_date','<', now()->toDateString())->count();

    $recentProjects = Project::latest()->limit(5)->get();
    $recentTasks = (clone $taskQuery)->with('project')->latest()->limit(5)->get();
@endphp

<style>
    .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; }
    .kpi-card { border-radius: 12px; padding: 16px; box-shadow: var(--shadow-lg); background: linear-gradient(135deg, #f8fafc, #eef2ff); }
    .kpi-title { font-size: .9rem; color: #475569; margin: 0 0 .35rem; display:flex; align-items:center; gap:.4rem }
    .kpi-value { font-size: 1.8rem; font-weight: 700; margin: 0; }
    .progress { height: 8px; border-radius: 999px; overflow: hidden; background: #e2e8f0; }
    .progress > span { display:block; height:100%; background: linear-gradient(135deg, #4f46e5, #06b6d4); }
    .section { margin-block: 1.25rem; }
    .section h2 { margin: 0 0 .75rem; }
    .actions { display: flex; gap: 8px; flex-wrap: wrap; }
    @media (max-width: 768px) { .kpi-value { font-size: 1.5rem; } }
    .muted { color:#64748b; font-size:.9rem }
    .pill { padding:.2rem .55rem; border-radius:999px; font-size:.7rem; background:#e2e8f0 }
    .pill.success { background:#16a34a20; color:#166534 }
    .pill.warn { background:#f59e0b20; color:#92400e }
</style>

<header class="section">
    <h1 style="display:flex;align-items:center;gap:.5rem"><i class="ri-dashboard-line icon"></i> Dashboard</h1>
    <div class="actions">
        <a href="{{ route('projects.index') }}" class="btn btn-sm btn-primary"><i class="ri-folder-3-line icon"></i> View Projects</a>
        <a href="{{ auth()->user()?->hasRole('developer') ? route('tasks.my') : route('tasks.index') }}" class="btn btn-sm btn-outline"><i class="ri-task-line icon"></i> View Tasks</a>
    </div>
</header>

<section class="kpi-grid">
    <article class="kpi-card">
        <div class="kpi-title"><i class="ri-folder-2-line"></i> Total Projects</div>
        <p class="kpi-value">{{ $projectsTotal }}</p>
        <div class="progress" aria-label="Projects Active">
            @php $pct = $projectsTotal ? round(($projectsActive / max(1,$projectsTotal))*100) : 0; @endphp
            <span style="width: {{ $pct }}%"></span>
        </div>
        <p class="muted" style="margin:.4rem 0 0">Active: {{ $projectsActive }} ({{ $pct }}%)</p>
    </article>

    <article class="kpi-card">
        <div class="kpi-title"><i class="ri-play-list-2-line"></i> Open Tasks</div>
        <p class="kpi-value">{{ $tasksOpen }}</p>
        <div class="progress" aria-label="Overdue">
            @php $overPct = $tasksOpen ? min(100, round(($tasksOverdue / max(1,$tasksOpen))*100)) : 0; @endphp
            <span style="width: {{ $overPct }}%; background: linear-gradient(135deg,#ef4444,#f59e0b);"></span>
        </div>
        <p class="muted" style="margin:.4rem 0 0">Overdue: {{ $tasksOverdue }} ({{ $overPct }}%)</p>
    </article>

    <article class="kpi-card">
        <div class="kpi-title"><i class="ri-time-line"></i> This Week</div>
        @php
            $weekStart = now()->startOfWeek();
            $weekEnd = now()->endOfWeek();
            $dueThisWeek = (clone $taskQuery)->whereBetween('due_date', [$weekStart, $weekEnd])->count();
        @endphp
        <p class="kpi-value">{{ $dueThisWeek }}</p>
        <p class="muted">Tasks due this week</p>
    </article>

    <article class="kpi-card">
        <div class="kpi-title"><i class="ri-user-follow-line"></i> My Focus</div>
        @php
            $myInProgress = (clone $taskQuery)->where('status','in_progress')->count();
            $myPending = (clone $taskQuery)->where('status','pending')->count();
        @endphp
        <p class="kpi-value">{{ $myInProgress }}</p>
        <p class="muted">In progress &middot; <span class="pill">Pending: {{ $myPending }}</span></p>
    </article>
</section>

<section class="section">
    <div class="grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:16px">
        <article style="border-radius:var(--radius-lg);box-shadow:var(--shadow-lg);padding:12px">
            <h2 style="display:flex;align-items:center;gap:.4rem"><i class="ri-history-line"></i> Recent Projects</h2>
            <div class="table-wrap">
                <table role="grid" class="premium compact">
                    <thead>
                        <tr><th>Name</th><th>Status</th><th>Owner</th></tr>
                    </thead>
                    <tbody>
                        @forelse($recentProjects as $p)
                            <tr>
                                <td><a href="{{ route('projects.show',$p) }}"><i class="ri-folder-3-line icon"></i> {{ $p->name }}</a></td>
                                <td><span class="badge {{ $p->status === 'completed' ? 'badge-success' : ($p->status === 'in_progress' ? 'badge-primary' : ($p->status === 'on_hold' ? 'badge-warning' : 'badge-secondary')) }}">{{ ucfirst(str_replace('_',' ',$p->status)) }}</span></td>
                                <td>{{ $p->owner?->name }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3">No data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <article style="border-radius:var(--radius-lg);box-shadow:var(--shadow-lg);padding:12px">
            <h2 style="display:flex;align-items:center;gap:.4rem"><i class="ri-history-line"></i> Recent Tasks</h2>
            <div class="table-wrap">
                <table role="grid" class="premium compact">
                    <thead>
                        <tr><th>Title</th><th>Project</th><th>Status</th><th>Due</th></tr>
                    </thead>
                    <tbody>
                        @forelse($recentTasks as $t)
                            <tr>
                                <td>{{ $t->title }}</td>
                                <td>{{ $t->project?->name }}</td>
                                <td><span class="badge {{ $t->status === 'completed' ? 'badge-success' : ($t->status === 'in_progress' ? 'badge-primary' : 'badge-warning') }}">{{ ucfirst(str_replace('_',' ',$t->status)) }}</span></td>
                                <td>{{ optional($t->due_date)->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4">No data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>
    </div>
</section>

@endsection
