<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Task\StoreTaskRequest;
use App\Http\Requests\Api\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Task::with(['project', 'assignee', 'creator']);

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority if provided
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by project if provided
        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Search by title
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }

        // If user is a developer, only show their tasks
        if ($request->user()->hasRole('developer')) {
            $query->where('assigned_to', $request->user()->id);
        }

        $tasks = $query->latest()->paginate(10);
        return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $task = new Task($request->validated());
            $task->created_by = $request->user()->id;
            $task->save();

            DB::commit();

            return response()->json([
                'message' => 'Task created successfully',
                'data' => new TaskResource($task->load(['project', 'assignee', 'creator'])),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create task',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task): TaskResource
    {
        $this->authorize('view', $task);
        return new TaskResource($task->load(['project', 'assignee', 'creator']));
    }

    /**
     * Update the specified task in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        try {
            DB::beginTransaction();

            $task->update($request->validated());

            DB::commit();

            return response()->json([
                'message' => 'Task updated successfully',
                'data' => new TaskResource($task->fresh(['project', 'assignee', 'creator'])),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update task',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Task $task): JsonResponse
    {
        $this->authorize('delete', $task);

        try {
            $task->delete();
            return response()->json(['message' => 'Task deleted successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete task',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark task as completed.
     */
    public function markAsCompleted(Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        try {
            $task->markAsCompleted();
            return response()->json([
                'message' => 'Task marked as completed',
                'data' => new TaskResource($task->fresh(['project', 'assignee', 'creator'])),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to mark task as completed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
