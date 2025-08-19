<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserService extends BaseService
{
    /**
     * @var UserRepositoryInterface
     */
    protected $repository;

    /**
     * UserService constructor.
     *
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        parent::__construct($userRepository);
        $this->repository = $userRepository;
    }

    /**
     * Create a new user with the given data.
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        return $this->repository->create($data);
    }

    /**
     * Update an existing user.
     *
     * @param int $id
     * @param array $data
     * @return User
     */
    public function updateUser(int $id, array $data): User
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return $this->repository->update($id, $data);
    }

    /**
     * Find a user by email.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return $this->repository->findByEmail($email);
    }

    /**
     * Get all users with a specific role.
     *
     * @param string $role
     * @return Collection
     */
    public function getUsersByRole(string $role)
    {
        return $this->repository->getByRole($role);
    }

    /**
     * Assign a role to a user.
     *
     * @param int $userId
     * @param int $roleId
     * @return void
     */
    public function assignRoleToUser(int $userId, int $roleId): void
    {
        $this->repository->assignRole($userId, $roleId);
    }

    /**
     * Revoke a role from a user.
     *
     * @param int $userId
     * @param int $roleId
     * @return void
     */
    public function revokeRoleFromUser(int $userId, int $roleId): void
    {
        $this->repository->revokeRole($userId, $roleId);
    }

    /**
     * Check if a user has a specific role.
     *
     * @param int $userId
     * @param string $roleName
     * @return bool
     */
    public function userHasRole(int $userId, string $roleName): bool
    {
        return $this->repository->hasRole($userId, $roleName);
    }

    /**
     * Get all users with their roles.
     *
     * @return Collection
     */
    public function getAllUsersWithRoles()
    {
        return $this->repository->getAllWithRoles();
    }

    /**
     * Search users by name or email.
     *
     * @param string $query
     * @return Collection
     */
    public function searchUsers(string $query)
    {
        return $this->repository->search($query);
    }

    /**
     * Get paginated users with filters.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedUsers(array $filters = [], int $perPage = 15)
    {
        return $this->repository->getPaginatedWithFilters($filters, $perPage);
    }

    /**
     * Update user's password.
     *
     * @param int $userId
     * @param string $newPassword
     * @return bool
     */
    public function updatePassword(int $userId, string $newPassword): bool
    {
        $user = $this->repository->findOrFail($userId);
        $user->password = Hash::make($newPassword);
        return $user->save();
    }

    /**
     * Toggle user's active status.
     *
     * @param int $userId
     * @return bool
     */
    public function toggleActiveStatus(int $userId): bool
    {
        $user = $this->repository->findOrFail($userId);
        $user->is_active = !$user->is_active;
        return $user->save();
    }
}
