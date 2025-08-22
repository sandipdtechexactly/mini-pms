<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Project\StoreProjectRequest;
use App\Http\Requests\Api\Project\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Project::with(['owner', 'teamMembers', 'tasks']);

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search by title
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }

        // If user is a developer, only show their projects
        if ($request->user()->hasRole('developer')) {
            $query->whereHas('teamMembers', function ($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            });
        }

        $projects = $query->latest()->paginate(10);
        return ProjectResource::collection($projects);
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $project = new Project($request->validated());
            $project->owner_id = $request->user()->id;
            $project->save();

            // Attach team members
            if ($request->has('team_members')) {
                $project->teamMembers()->sync($request->team_members);
            }

            DB::commit();

            return response()->json([
                'message' => 'Project created successfully',
                'data' => new ProjectResource($project->load(['owner', 'teamMembers'])),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create project',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project): ProjectResource
    {
        $this->authorize('view', $project);
        return new ProjectResource($project->load(['owner', 'teamMembers', 'tasks']));
    }

    /**
     * Update the specified project in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $this->authorize('update', $project);

        try {
            DB::beginTransaction();

            $project->update($request->validated());

            // Update team members if provided
            if ($request->has('team_members')) {
                $project->teamMembers()->sync($request->team_members);
            }

            DB::commit();

            return response()->json([
                'message' => 'Project updated successfully',
                'data' => new ProjectResource($project->fresh(['owner', 'teamMembers'])),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update project',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified project from storage.
     */
    public function destroy(Project $project): JsonResponse
    {
        $this->authorize('delete', $project);

        try {
            $project->delete();
            return response()->json(['message' => 'Project deleted successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete project',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
