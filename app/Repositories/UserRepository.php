<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * Get the model class name.
     *
     * @return string
     */
    protected function model()
    {
        return User::class;
    }

    /**
     * @inheritDoc
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * @inheritDoc
     */
    public function getByRole(string $role)
    {
        return $this->model->whereHas('roles', function ($query) use ($role) {
            $query->where('name', $role);
        })->get();
    }

    /**
     * @inheritDoc
     */
    public function assignRole(int $userId, int $roleId): void
    {
        $user = $this->findOrFail($userId);
        $user->roles()->syncWithoutDetaching([$roleId]);
    }

    /**
     * @inheritDoc
     */
    public function revokeRole(int $userId, int $roleId): void
    {
        $user = $this->findOrFail($userId);
        $user->roles()->detach($roleId);
    }

    /**
     * @inheritDoc
     */
    public function hasRole(int $userId, string $roleName): bool
    {
        $user = $this->findOrFail($userId);
        return $user->roles()->where('name', $roleName)->exists();
    }

    /**
     * @inheritDoc
     */
    public function getAllWithRoles()
    {
        return $this->model->with('roles')->get();
    }

    /**
     * Search users by name or email.
     *
     * @param string $query
     * @return Collection
     */
    public function search(string $query)
    {
        return $this->model->where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->get();
    }

    /**
     * Get users with pagination and optional filters.
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginatedWithFilters(array $filters = [], int $perPage = 15)
    {
        $query = $this->model->with('roles');

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (isset($filters['role'])) {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('name', $filters['role']);
            });
        }

        if (isset($filters['status'])) {
            $query->where('is_active', $filters['status'] === 'active');
        }

        return $query->paginate($perPage);
    }
}
