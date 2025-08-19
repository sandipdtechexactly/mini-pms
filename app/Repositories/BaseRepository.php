<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

abstract class BaseRepository
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     */
    public function __construct()
    {
        $this->makeModel();
    }

    /**
     * Get the model class name.
     *
     * @return string
     */
    abstract protected function model();

    /**
     * Make model instance.
     *
     * @return Model
     * @throws \Exception
     */
    protected function makeModel()
    {
        $model = app($this->model());

        if (!$model instanceof Model) {
            throw new \Exception("Class {$this->model()} must be an instance of " . Model::class);
        }

        return $this->model = $model;
    }

    /**
     * Get all records.
     *
     * @param array $columns
     * @return Collection
     */
    public function all($columns = ['*'])
    {
        return $this->model->get($columns);
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
        return $this->model->find($id, $columns);
    }

    /**
     * Find or fail a record by ID.
     *
     * @param int $id
     * @param array $columns
     * @return Model
     * @throws ModelNotFoundException
     */
    public function findOrFail($id, $columns = ['*'])
    {
        return $this->model->findOrFail($id, $columns);
    }

    /**
     * Create a new record.
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data)
    {
        return $this->model->create($data);
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
        $record = $this->findOrFail($id);
        $record->update($data);
        return $record->fresh();
    }

    /**
     * Delete a record.
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $record = $this->findOrFail($id);
        return $record->delete();
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
        return $this->model->paginate($perPage, $columns);
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
        return $this->model->where($conditions)->get($columns);
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
        return $this->model->where($conditions)->first($columns);
    }
}
