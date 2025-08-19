<?php

namespace App\Services;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class BaseService
{
    /**
     * @var BaseRepository
     */
    protected $repository;

    /**
     * BaseService constructor.
     *
     * @param BaseRepository $repository
     */
    public function __construct(BaseRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all records.
     *
     * @param array $columns
     * @return Collection
     */
    public function all($columns = ['*'])
    {
        return $this->repository->all($columns);
    }

    /**
     * Find a record by ID.
     *
     * @param int $id
     * @param array $columns
     * @return Model|null
     */
    public function find($id, $columns = ['*'])
    {
        return $this->repository->find($id, $columns);
    }

    /**
     * Find or fail a record by ID.
     *
     * @param int $id
     * @param array $columns
     * @return Model
     */
    public function findOrFail($id, $columns = ['*'])
    {
        return $this->repository->findOrFail($id, $columns);
    }

    /**
     * Create a new record.
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    /**
     * Update a record.
     *
     * @param int $id
     * @param array $data
     * @return Model
     */
    public function update($id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Delete a record.
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    /**
     * Get records with pagination.
     *
     * @param int $perPage
     * @param array $columns
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage = 15, $columns = ['*'])
    {
        return $this->repository->paginate($perPage, $columns);
    }

    /**
     * Get records with conditions.
     *
     * @param array $conditions
     * @param array $columns
     * @return Collection
     */
    public function where(array $conditions, $columns = ['*'])
    {
        return $this->repository->where($conditions, $columns);
    }

    /**
     * Get the first record matching the conditions.
     *
     * @param array $conditions
     * @param array $columns
     * @return Model|null
     */
    public function firstWhere(array $conditions, $columns = ['*'])
    {
        return $this->repository->firstWhere($conditions, $columns);
    }
}
