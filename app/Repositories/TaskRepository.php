<?php

namespace App\Repositories;

use App\Models\Task;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TaskRepository extends BaseRepository implements TaskRepositoryInterface
{
    /**
     * Get the model class name.
     *
     * @return string
     */
    protected function model()
    {
        return Task::class;
    }

    /**
     * Get all tasks with optional filters.
     *
     * @param array $filters
     * @param array $with
     * @return Collection|LengthAwarePaginator
     */
    public function getAll(array $filters = [], array $with = [])
    {
        $query = $this->model->with($with);
        
        // Apply filters if any
        if (!empty($filters)) {
            $query = $this->applyFilters($query, $filters);
        }
        
        // Check if pagination is requested
        if (isset($filters['per_page'])) {
            return $query->paginate($filters['per_page']);
        }
        
        return $query->get();
    }

    /**
     * Find a task by ID.
     *
     * @param int $id
     * @param array $with
     * @return Task|null
     */
    public function findById(int $id, array $with = []): ?Task
    {
        return $this->model->with($with)->findOrFail($id);
    }

    /**
     * Create a new task.
     *
     * @param array $data
     * @return Task
     */
    public function create(array $data): Task
    {
        return $this->model->create($data);
    }

    /**
     * Update a task.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $task = $this->findById($id);
        return $task->update($data);
    }

    /**
     * Delete a task.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $task = $this->findById($id);
        return $task->delete();
    }

    /**
     * Get tasks by project ID.
     *
     * @param int $projectId
     * @param array $filters
     * @return Collection|LengthAwarePaginator
     */
    public function getByProjectId(int $projectId, array $filters = [])
    {
        $query = $this->model->where('project_id', $projectId);
        
        if (!empty($filters)) {
            $query = $this->applyFilters($query, $filters);
        }
        
        if (isset($filters['per_page'])) {
            return $query->paginate($filters['per_page']);
        }
        
        return $query->get();
    }

    /**
     * Get tasks assigned to a user.
     *
     * @param int $userId
     * @param array $filters
     * @return Collection|LengthAwarePaginator
     */
    public function getAssignedToUser(int $userId, array $filters = [])
    {
        $query = $this->model->where('assigned_to', $userId);
        
        if (!empty($filters)) {
            $query = $this->applyFilters($query, $filters);
        }
        
        if (isset($filters['per_page'])) {
            return $query->paginate($filters['per_page']);
        }
        
        return $query->get();
    }

    /**
     * Get tasks by status.
     *
     * @param string $status
     * @param array $filters
     * @return Collection|LengthAwarePaginator
     */
    public function getByStatus(string $status, array $filters = [])
    {
        $query = $this->model->where('status', $status);
        
        if (!empty($filters)) {
            $query = $this->applyFilters($query, $filters);
        }
        
        if (isset($filters['per_page'])) {
            return $query->paginate($filters['per_page']);
        }
        
        return $query->get();
    }

    /**
     * Search tasks by title or description.
     *
     * @param string $query
     * @param array $filters
     * @return Collection|LengthAwarePaginator
     */
    public function search(string $query, array $filters = [])
    {
        $searchQuery = $this->model->where(function ($q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%");
        });
        
        if (!empty($filters)) {
            $searchQuery = $this->applyFilters($searchQuery, $filters);
        }
        
        if (isset($filters['per_page'])) {
            return $searchQuery->paginate($filters['per_page']);
        }
        
        return $searchQuery->get();
    }

    /**
     * Get tasks with advanced filters.
     *
     * @param array $filters
     * @return Collection|LengthAwarePaginator
     */
    public function filter(array $filters = [])
    {
        $query = $this->model->query();
        
        if (!empty($filters)) {
            $query = $this->applyFilters($query, $filters);
        }
        
        if (isset($filters['per_page'])) {
            return $query->paginate($filters['per_page']);
        }
        
        return $query->get();
    }

    /**
     * Get task statistics.
     *
     * @param array $filters
     * @return array
     */
    public function getStatistics(array $filters = []): array
    {
        $query = $this->model->query();
        
        if (!empty($filters)) {
            $query = $this->applyFilters($query, $filters);
        }
        
        $total = $query->count();
        
        $byStatus = $this->model->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
            
        $byPriority = $this->model->select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();
            
        return [
            'total_tasks' => $total,
            'by_status' => $byStatus,
            'by_priority' => $byPriority,
            'completed_tasks' => $byStatus[Task::STATUS_COMPLETED] ?? 0,
            'pending_tasks' => $total - ($byStatus[Task::STATUS_COMPLETED] ?? 0),
        ];
    }

    /**
     * Apply filters to the query.
     *
     * @param Builder $query
     * @param array $filters
     * @return Builder
     */
    protected function applyFilters(Builder $query, array $filters): Builder
    {
        // Filter by project_id
        if (isset($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }
        
        // Filter by assigned_to (user ID)
        if (isset($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }
        
        // Filter by status
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        // Filter by priority
        if (isset($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }
        
        // Filter by due date range
        if (isset($filters['due_date_from'])) {
            $query->whereDate('due_date', '>=', $filters['due_date_from']);
        }
        
        if (isset($filters['due_date_to'])) {
            $query->whereDate('due_date', '<=', $filters['due_date_to']);
        }
        
        // Search by title or description
        if (isset($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }
        
        // Order by
        $orderBy = $filters['order_by'] ?? 'created_at';
        $orderDirection = $filters['order_direction'] ?? 'desc';
        $query->orderBy($orderBy, $orderDirection);
        
        return $query;
    }
}
