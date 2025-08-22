@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto p-6">
        <h1 class="text-2xl font-semibold mb-6">Edit Project</h1>

        @if ($errors->any())
            <div class="mb-4 text-red-600">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('projects.update', $project) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-medium">Name</label>
                <input name="name" value="{{ old('name', $project->name) }}" class="w-full border rounded p-2" required />
            </div>

            <div>
                <label class="block font-medium">Code</label>
                <input name="code" value="{{ old('code', $project->code) }}" class="w-full border rounded p-2" required />
            </div>

            <div>
                <label class="block font-medium">Description</label>
                <textarea name="description" class="w-full border rounded p-2" rows="4">{{ old('description', $project->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium">Status</label>
                    <select name="status" class="w-full border rounded p-2" required>
                        @foreach (['planning','in_progress','on_hold','completed','cancelled'] as $status)
                            <option value="{{ $status }}" @selected(old('status', $project->status) === $status)>{{ ucfirst(str_replace('_',' ', $status)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-medium">Priority</label>
                    <select name="priority" class="w-full border rounded p-2" required>
                        @foreach (['low','medium','high','urgent'] as $priority)
                            <option value="{{ $priority }}" @selected(old('priority', $project->priority) === $priority)>{{ ucfirst($priority) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium">Start Date</label>
                    <input type="date" name="start_date" value="{{ old('start_date', optional($project->start_date)->format('Y-m-d')) }}" class="w-full border rounded p-2" required />
                </div>
                <div>
                    <label class="block font-medium">End Date</label>
                    <input type="date" name="end_date" value="{{ old('end_date', optional($project->end_date)->format('Y-m-d')) }}" class="w-full border rounded p-2" required />
                </div>
            </div>

            <div>
                <label class="block font-medium">Budget</label>
                <input type="number" step="0.01" name="budget" value="{{ old('budget', $project->budget) }}" class="w-full border rounded p-2" />
            </div>

            <div>
                <label class="block font-medium">Team Members</label>
                <select name="team_members[]" class="w-full border rounded p-2" multiple>
                    @php $selected = $project->teamMembers->pluck('id')->all(); @endphp
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(in_array($user->id, old('team_members', $selected)))>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                <p class="text-sm text-gray-600 mt-1">Hold Ctrl (Windows) or Cmd (Mac) to select multiple users.</p>
            </div>

            <div class="pt-4">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
                <a href="{{ route('projects.index') }}" class="ml-2 px-4 py-2 border rounded">Cancel</a>
            </div>
        </form>
    </div>
@endsection


