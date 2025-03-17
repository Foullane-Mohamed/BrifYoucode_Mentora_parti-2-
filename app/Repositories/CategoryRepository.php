<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    public function getWithSubcategories($id)
    {
        return $this->model->with('subcategories')->findOrFail($id);
    }

    public function getWithCourses($id)
    {
        return $this->model->with('courses')->findOrFail($id);
    }
}