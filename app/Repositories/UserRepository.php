<?php

namespace App\Repositories;

use App\Models\Role;
use App\Models\User;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * UserRepository constructor.
     * 
     * @param User $model
     */
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * @inheritDoc
     */
    public function getUsersByRole(string $role): Collection
    {
        return $this->model->whereHas('roles', function ($query) use ($role) {
            $query->where('name', $role);
        })->get();
    }

    /**
     * @inheritDoc
     */
    public function assignRole(int $userId, string $roleName): bool
    {
        $user = $this->findById($userId);
        $role = Role::where('name', $roleName)->firstOrFail();
        
        if (!$user->hasRole($roleName)) {
            $user->roles()->attach($role);
            return true;
        }
        
        return false;
    }

    /**
     * @inheritDoc
     */
    public function removeRole(int $userId, string $roleName): bool
    {
        $user = $this->findById($userId);
        $role = Role::where('name', $roleName)->firstOrFail();
        
        if ($user->hasRole($roleName)) {
            $user->roles()->detach($role);
            return true;
        }
        
        return false;
    }

    /**
     * @inheritDoc
     */
    public function hasRole(int $userId, string $roleName): bool
    {
        $user = $this->findById($userId);
        return $user->hasRole($roleName);
    }

    /**
     * @inheritDoc
     */
    public function hasPermission(int $userId, string $permissionName): bool
    {
        $user = $this->findById($userId);
        return $user->hasPermission($permissionName);
    }
}