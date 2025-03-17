<?php

namespace App\Repositories\Interfaces;

interface CategoryRepositoryInterface extends BaseRepositoryInterface
{
    public function getWithSubcategories($id);
    public function getWithCourses($id);
}