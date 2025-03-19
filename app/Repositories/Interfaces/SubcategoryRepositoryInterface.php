<?php

namespace App\Repositories\Interfaces;

use App\Models\Subcategory;
use Illuminate\Database\Eloquent\Collection;

interface SubcategoryRepositoryInterface extends EloquentRepositoryInterface
{
    /**
     * Get active subcategories.
     * 
     * @return Collection
     */
    public function getActiveSubcategories(): Collection;

    /**
     * Get subcategory by slug.
     * 
     * @param string $slug
     * @return Subcategory|null
     */
    public function getSubcategoryBySlug(string $slug): ?Subcategory;

    /**
     * Get subcategories by category.
     * 
     * @param int $categoryId
     * @return Collection
     */
    public function getSubcategoriesByCategory(int $categoryId): Collection;

    /**
     * Get subcategory with courses.
     * 
     * @param int $subcategoryId
     * @return Subcategory|null
     */
    public function getSubcategoryWithCourses(int $subcategoryId): ?Subcategory;
}