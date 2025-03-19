<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    /**
     * CategoryRepository constructor.
     * 
     * @param Category $model
     */
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    /**
     * @inheritDoc
     */
    public function getActiveCategories(): Collection
    {
        return $this->model->where('is_active', true)->get();
    }

    /**
     * @inheritDoc
     */
    public function getCategoryBySlug(string $slug): ?Category
    {
        return $this->model->where('slug', $slug)->first();
    }

    /**
     * @inheritDoc
     */
    public function getCategoriesWithSubcategories(): Collection
    {
        return $this->model->with('subcategories')->get();
    }

    /**
     * @inheritDoc
     */
    public function getCategoryWithCourses(int $categoryId): ?Category
    {
        return $this->model->with(['subcategories.courses' => function ($query) {
            $query->where('is_published', true);
        }])->findOrFail($categoryId);
    }
}