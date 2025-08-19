<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface UserRepositoryInterface
{
    /**
     * Find a user by email.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User;

    /**
     * Get all users with a specific role.
     *
     * @param string $role
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByRole(string $role);

    /**
     * Assign a role to a user.
     *
     * @param int $userId
     * @param int $roleId
     * @return void
     */
    public function assignRole(int $userId, int $roleId): void;

    /**
     * Revoke a role from a user.
     *
     * @param int $userId
     * @param int $roleId
     * @return void
     */
    public function revokeRole(int $userId, int $roleId): void;

    /**
     * Check if a user has a specific role.
     *
     * @param int $userId
     * @param string $roleName
     * @return bool
     */
    public function hasRole(int $userId, string $roleName): bool;

    /**
     * Get all users with their roles.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWithRoles();
}
