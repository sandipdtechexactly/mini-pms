<?php

namespace App\Repositories\Interfaces;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface TaskRepositoryInterface
{
    /**
     * Get all tasks with optional filters.
     *
     * @param array $filters
     * @param array $with
     * @return Collection|LengthAwarePaginator
     */
    public function getAll(array $filters = [], array $with = []);

    /**
     * Find a task by ID.
     *
     * @param int $id
     * @param array $with
     * @return Task|null
     */
    public function findById(int $id, array $with = []): ?Task;

    /**
     * Create a new task.
     *
     * @param array $data
     * @return Task
     */
    public function create(array $data): Task;

    /**
     * Update a task.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete a task.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Get tasks by project ID.
     *
     * @param int $projectId
     * @param array $filters
     * @return Collection|LengthAwarePaginator
     */
    public function getByProjectId(int $projectId, array $filters = []);

    /**
     * Get tasks assigned to a user.
     *
     * @param int $userId
     * @param array $filters
     * @return Collection|LengthAwarePaginator
     */
    public function getAssignedToUser(int $userId, array $filters = []);

    /**
     * Get tasks by status.
     *
     * @param string $status
     * @param array $filters
     * @return Collection|LengthAwarePaginator
     */
    public function getByStatus(string $status, array $filters = []);

    /**
     * Search tasks by title or description.
     *
     * @param string $query
     * @param array $filters
     * @return Collection|LengthAwarePaginator
     */
    public function search(string $query, array $filters = []);

    /**
     * Get tasks with advanced filters.
     *
     * @param array $filters
     * @return Collection|LengthAwarePaginator
     */
    public function filter(array $filters = []);

    /**
     * Get task statistics.
     *
     * @param array $filters
     * @return array
     */
    public function getStatistics(array $filters = []): array;
}
