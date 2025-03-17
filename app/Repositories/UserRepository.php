<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function getByRole($roleId)
    {
        return $this->model->where('role_id', $roleId)->get();
    }

    public function getWithProfile($id)
    {
        return $this->model->with('profile')->findOrFail($id);
    }

    public function getWithCourses($id)
    {
        return $this->model->with('courses')->findOrFail($id);
    }

    public function getWithEnrollments($id)
    {
        return $this->model->with('enrollments')->findOrFail($id);
    }

    public function getWithBadges($id)
    {
        return $this->model->with('badges')->findOrFail($id);
    }
}