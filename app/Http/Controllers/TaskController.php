<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskController extends Controller
{
    use AuthorizesRequests;
    
    /**
     * Display a listing of all tasks (for admin/manager).
     */
    public function index()
    {
        $this->authorize('viewAny', Task::class);
        
        $tasks = Task::with(['project', 'assignedTo', 'createdBy'])
            ->latest()
            ->paginate(15);
            
        return view('tasks.index', compact('tasks'));
    }

    /**
     * Display a listing of tasks assigned to the authenticated developer.
     */
    public function myTasks()
    {
        $tasks = Task::with(['project', 'assignedTo', 'createdBy'])
            ->where('assigned_to', Auth::id())
            ->latest()
            ->paginate(15);
            
        return view('tasks.my-tasks', compact('tasks'));
    }
}
