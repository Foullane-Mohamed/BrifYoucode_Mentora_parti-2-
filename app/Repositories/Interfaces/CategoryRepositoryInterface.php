<?php

namespace App\Repositories\Interfaces;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface extends EloquentRepositoryInterface
{
    /**
     * Get active categories.
     * 
     * @return Collection
     */
    public function getActiveCategories(): Collection;

    /**
     * Get category by slug.
     * 
     * @param string $slug
     * @return Category|null
     */
    public function getCategoryBySlug(string $slug): ?Category;

    /**
     * Get categories with subcategories.
     * 
     * @return Collection
     */
    public function getCategoriesWithSubcategories(): Collection;

    /**
     * Get category with courses.
     * 
     * @param int $categoryId
     * @return Category|null
     */
    public function getCategoryWithCourses(int $categoryId): ?Category;
}