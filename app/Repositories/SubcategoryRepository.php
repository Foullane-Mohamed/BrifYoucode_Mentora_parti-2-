<?php

namespace App\Repositories;

use App\Models\Subcategory;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\SubcategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SubcategoryRepository extends BaseRepository implements SubcategoryRepositoryInterface
{
    /**
     * SubcategoryRepository constructor.
     * 
     * @param Subcategory $model
     */
    public function __construct(Subcategory $model)
    {
        parent::__construct($model);
    }

    /**
     * @inheritDoc
     */
    public function getActiveSubcategories(): Collection
    {
        return $this->model->where('is_active', true)->get();
    }

    /**
     * @inheritDoc
     */
    public function getSubcategoryBySlug(string $slug): ?Subcategory
    {
        return $this->model->where('slug', $slug)->first();
    }

    /**
     * @inheritDoc
     */
    public function getSubcategoriesByCategory(int $categoryId): Collection
    {
        return $this->model->where('category_id', $categoryId)->get();
    }

    /**
     * @inheritDoc
     */
    public function getSubcategoryWithCourses(int $subcategoryId): ?Subcategory
    {
        return $this->model->with(['courses' => function ($query) {
            $query->where('is_published', true);
        }])->findOrFail($subcategoryId);
    }
}