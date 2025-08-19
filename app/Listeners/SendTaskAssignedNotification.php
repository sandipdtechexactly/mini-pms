<?php

namespace App\Listeners;

use App\Events\TaskAssigned;
use App\Mail\TaskAssignedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendTaskAssignedNotification implements ShouldQueue
{
    /**
     * The number of times the queued listener may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 60;

    /**
     * Handle the event.
     *
     * @param  TaskAssigned  $event
     * @return void
     */
    public function handle(TaskAssigned $event): void
    {
        // Send email notification to the assigned user
        Mail::to($event->assignee->email)
            ->queue(new TaskAssignedMail(
                $event->task,
                $event->assignee,
                $event->assignedBy
            ));
    }

    /**
     * Handle a job failure.
     *
     * @param  TaskAssigned  $event
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(TaskAssigned $event, $exception)
    {
        // Log the failure or take other actions
        \Log::error('Failed to send task assigned notification', [
            'task_id' => $event->task->id,
            'assignee_id' => $event->assignee->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
