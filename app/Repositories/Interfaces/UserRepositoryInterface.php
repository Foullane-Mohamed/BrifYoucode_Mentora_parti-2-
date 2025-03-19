<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface extends EloquentRepositoryInterface
{
    /**
     * Get users by role.
     * 
     * @param string $role
     * @return Collection
     */
    public function getUsersByRole(string $role): Collection;

    /**
     * Assign role to user.
     * 
     * @param int $userId
     * @param string $roleName
     * @return bool
     */
    public function assignRole(int $userId, string $roleName): bool;

    /**
     * Remove role from user.
     * 
     * @param int $userId
     * @param string $roleName
     * @return bool
     */
    public function removeRole(int $userId, string $roleName): bool;

    /**
     * Check if user has a specific role.
     * 
     * @param int $userId
     * @param string $roleName
     * @return bool
     */
    public function hasRole(int $userId, string $roleName): bool;

    /**
     * Check if user has a specific permission.
     * 
     * @param int $userId
     * @param string $permissionName
     * @return bool
     */
    public function hasPermission(int $userId, string $permissionName): bool;
}