<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TaskService extends BaseService
{
    /**
     * @var TaskRepositoryInterface
     */
    protected $repository;

    /**
     * TaskService constructor.
     *
     * @param TaskRepositoryInterface $taskRepository
     */
    public function __construct(TaskRepositoryInterface $taskRepository)
    {
        parent::__construct($taskRepository);
        $this->repository = $taskRepository;
    }

    /**
     * Get all tasks with optional filters.
     *
     * @param array $filters
     * @param array $with
     * @return Collection|LengthAwarePaginator
     */
    public function getAllTasks(array $filters = [], array $with = [])
    {
        return $this->repository->getAll($filters, $with);
    }

    /**
     * Get a task by ID.
     *
     * @param int $id
     * @param array $with
     * @return Task
     */
    public function getTaskById(int $id, array $with = []): Task
    {
        return $this->repository->findById($id, $with);
    }

    /**
     * Create a new task.
     *
     * @param array $data
     * @return Task
     */
    public function createTask(array $data): Task
    {
        return $this->repository->create($data);
    }

    /**
     * Update a task.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateTask(int $id, array $data): bool
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Delete a task.
     *
     * @param int $id
     * @return bool
     */
    public function deleteTask(int $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Get tasks by project ID.
     *
     * @param int $projectId
     * @param array $filters
     * @return Collection|LengthAwarePaginator
     */
    public function getTasksByProject(int $projectId, array $filters = [])
    {
        return $this->repository->getByProjectId($projectId, $filters);
    }

    /**
     * Get tasks assigned to a user.
     *
     * @param int $userId
     * @param array $filters
     * @return Collection|LengthAwarePaginator
     */
    public function getTasksAssignedToUser(int $userId, array $filters = [])
    {
        return $this->repository->getAssignedToUser($userId, $filters);
    }

    /**
     * Get tasks by status.
     *
     * @param string $status
     * @param array $filters
     * @return Collection|LengthAwarePaginator
     */
    public function getTasksByStatus(string $status, array $filters = [])
    {
        return $this->repository->getByStatus($status, $filters);
    }

    /**
     * Search tasks by title or description.
     *
     * @param string $query
     * @param array $filters
     * @return Collection|LengthAwarePaginator
     */
    public function searchTasks(string $query, array $filters = [])
    {
        return $this->repository->search($query, $filters);
    }

    /**
     * Get tasks with advanced filters.
     *
     * @param array $filters
     * @return Collection|LengthAwarePaginator
     */
    public function filterTasks(array $filters = [])
    {
        return $this->repository->filter($filters);
    }

    /**
     * Get task statistics.
     *
     * @param array $filters
     * @return array
     */
    public function getTaskStatistics(array $filters = []): array
    {
        return $this->repository->getStatistics($filters);
    }

    /**
     * Change task status.
     *
     * @param int $taskId
     * @param string $status
     * @return bool
     */
    public function changeTaskStatus(int $taskId, string $status): bool
    {
        $validStatuses = [
            Task::STATUS_PENDING,
            Task::STATUS_IN_PROGRESS,
            Task::STATUS_IN_REVIEW,
            Task::STATUS_COMPLETED,
            Task::STATUS_BLOCKED,
        ];

        if (!in_array($status, $validStatuses)) {
            throw new \InvalidArgumentException("Invalid status provided");
        }

        return $this->repository->update($taskId, ['status' => $status]);
    }

    use Illuminate\Support\Facades\Event;
    use App\Events\TaskAssigned;
    use App\Models\User;

    /**
     * Assign a task to a user.
     *
     * @param int $taskId
     * @param int $userId
     * @param int $assignedById The ID of the user who is assigning the task
     * @return bool
     */
    public function assignTask(int $taskId, int $userId, int $assignedById): bool
    {
        $task = $this->repository->findById($taskId);
        $assignee = User::findOrFail($userId);
        $assignedBy = User::findOrFail($assignedById);
        
        $result = $this->repository->update($taskId, ['assigned_to' => $userId]);
        
        // Dispatch the TaskAssigned event
        if ($result) {
            Event::dispatch(new TaskAssigned($task, $assignee, $assignedBy));
        }
        
        return $result;
    }

    /**
     * Update task priority.
     *
     * @param int $taskId
     * @param string $priority
     * @return bool
     */
    public function updateTaskPriority(int $taskId, string $priority): bool
    {
        $validPriorities = [
            Task::PRIORITY_LOW,
            Task::PRIORITY_MEDIUM,
            Task::PRIORITY_HIGH,
            Task::PRIORITY_CRITICAL,
        ];

        if (!in_array($priority, $validPriorities)) {
            throw new \InvalidArgumentException("Invalid priority provided");
        }

        return $this->repository->update($taskId, ['priority' => $priority]);
    }

    /**
     * Get tasks that are due soon.
     *
     * @param int $days
     * @param array $filters
     * @return Collection|LengthAwarePaginator
     */
    public function getDueSoonTasks(int $days = 7, array $filters = [])
    {
        $filters['due_date_from'] = now()->toDateString();
        $filters['due_date_to'] = now()->addDays($days)->toDateString();
        $filters['status'] = Task::STATUS_PENDING;
        
        return $this->repository->filter($filters);
    }

    /**
     * Get overdue tasks.
     *
     * @param array $filters
     * @return Collection|LengthAwarePaginator
     */
    public function getOverdueTasks(array $filters = [])
    {
        $filters['due_date_to'] = now()->subDay()->toDateString();
        $filters['status'] = [Task::STATUS_PENDING, Task::STATUS_IN_PROGRESS, Task::STATUS_IN_REVIEW];
        
        return $this->repository->filter($filters);
    }
}
