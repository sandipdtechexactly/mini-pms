<?php

namespace App\Mail;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The task instance.
     *
     * @var Task
     */
    public $task;

    /**
     * The user who is assigned the task.
     *
     * @var User
     */
    public $assignee;

    /**
     * The user who assigned the task.
     *
     * @var User
     */
    public $assignedBy;

    /**
     * Create a new message instance.
     *
     * @param Task $task
     * @param User $assignee
     * @param User $assignedBy
     */
    public function __construct(Task $task, User $assignee, User $assignedBy)
    {
        $this->task = $task;
        $this->assignee = $assignee;
        $this->assignedBy = $assignedBy;
    }

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Task Assigned: ' . $this->task->title,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return Content
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.tasks.assigned',
            with: [
                'task' => $this->task,
                'assignee' => $this->assignee,
                'assignedBy' => $this->assignedBy,
                'taskUrl' => route('tasks.show', $this->task->id),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
