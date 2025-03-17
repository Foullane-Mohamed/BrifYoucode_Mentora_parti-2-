<?php

namespace App\Repositories\Interfaces;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function getByRole($roleId);
    public function getWithProfile($id);
    public function getWithCourses($id);
    public function getWithEnrollments($id);
    public function getWithBadges($id);
}