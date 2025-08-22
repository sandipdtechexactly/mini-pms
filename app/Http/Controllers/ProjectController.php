<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    /**
     * Display a listing of the projects.
     */
    public function index()
    {
        $this->authorize('viewAny', Project::class);
        
        $projects = Project::with(['owner', 'teamMembers'])
            ->latest()
            ->paginate(15);
            
        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create()
    {
        $this->authorize('create', Project::class);
        
        $users = User::where('id', '!=', Auth::id())->get();
        return view('projects.create', compact('users'));
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Project::class);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:planning,in_progress,on_hold,completed,cancelled',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'team_members' => 'sometimes|array',
            'team_members.*' => 'exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            $project = new Project($validated);
            $project->owner_id = Auth::id();
            $project->save();

            if (isset($validated['team_members'])) {
                $project->teamMembers()->sync($validated['team_members']);
            }

            DB::commit();

            return redirect()->route('projects.index')
                ->with('success', 'Project created successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create project: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project)
    {
        $this->authorize('view', $project);
        
        $project->load(['owner', 'teamMembers', 'tasks' => function($query) {
            $query->with('assignedTo')->latest();
        }]);
        
        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project)
    {
        $this->authorize('update', $project);
        
        $users = User::where('id', '!=', Auth::id())->get();
        $project->load('teamMembers');
        
        return view('projects.edit', compact('project', 'users'));
    }

    /**
     * Update the specified project in storage.
     */
    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:planning,in_progress,on_hold,completed,cancelled',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'team_members' => 'sometimes|array',
            'team_members.*' => 'exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            $project->update($validated);

            if (isset($validated['team_members'])) {
                $project->teamMembers()->sync($validated['team_members']);
            }

            DB::commit();

            return redirect()->route('projects.index')
                ->with('success', 'Project updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update project: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified project from storage.
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        
        try {
            $project->delete();
            return redirect()->route('projects.index')
                ->with('success', 'Project deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete project: ' . $e->getMessage());
        }
    }
}
