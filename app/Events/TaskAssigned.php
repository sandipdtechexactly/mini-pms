<?php

namespace App\Events;

use App\Models\Task;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskAssigned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $task;
    public $assignee;
    public $assignedBy;

    /**
     * Create a new event instance.
     *
     * @param Task $task
     * @param User $assignee
     * @param User $assignedBy
     */
    public function __construct(Task $task, User $assignee, User $assignedBy)
    {
        $this->task = $task->load(['project', 'creator']);
        $this->assignee = $assignee;
        $this->assignedBy = $assignedBy;
    }
}
