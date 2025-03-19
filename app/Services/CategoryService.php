<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryService
{
    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * CategoryService constructor.
     * 
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Get all categories.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllCategories()
    {
        return $this->categoryRepository->all();
    }

    /**
     * Get active categories.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveCategories()
    {
        return $this->categoryRepository->getActiveCategories();
    }

    /**
     * Get category by ID.
     * 
     * @param int $id
     * @return Category
     */
    public function getCategoryById($id)
    {
        return $this->categoryRepository->findById($id);
    }

    /**
     * Get category by slug.
     * 
     * @param string $slug
     * @return Category|null
     */
    public function getCategoryBySlug($slug)
    {
        return $this->categoryRepository->getCategoryBySlug($slug);
    }

    /**
     * Get categories with subcategories.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCategoriesWithSubcategories()
    {
        return $this->categoryRepository->getCategoriesWithSubcategories();
    }

    /**
     * Get category with courses.
     * 
     * @param int $categoryId
     * @return Category|null
     */
    public function getCategoryWithCourses($categoryId)
    {
        return $this->categoryRepository->getCategoryWithCourses($categoryId);
    }

    /**
     * Create new category.
     * 
     * @param array $data
     * @return Category
     */
    public function createCategory(array $data)
    {
        // Generate slug
        $data['slug'] = Str::slug($data['name']);
        
        // Handle image if provided
        if (isset($data['image']) && $data['image']) {
            $image = $data['image'];
            $filename = Str::random(20) . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/categories', $filename);
            $data['image'] = 'categories/' . $filename;
        }
        
        // Default is_active to true if not provided
        if (!isset($data['is_active'])) {
            $data['is_active'] = true;
        }
        
        return $this->categoryRepository->create($data);
    }

    /**
     * Update category.
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateCategory($id, array $data)
    {
        $category = $this->categoryRepository->findById($id);
        
        // Generate slug if name is provided
        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        
        // Handle image if provided
        if (isset($data['image']) && $data['image']) {
            // Delete old image
            if ($category->image && Storage::exists('public/' . $category->image)) {
                Storage::delete('public/' . $category->image);
            }
            
            $image = $data['image'];
            $filename = Str::random(20) . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/categories', $filename);
            $data['image'] = 'categories/' . $filename;
        }
        
        return $this->categoryRepository->update($id, $data);
    }

    /**
     * Delete category.
     * 
     * @param int $id
     * @return bool
     */
    public function deleteCategory($id)
    {
        $category = $this->categoryRepository->findById($id);
        
        // Delete image if exists
        if ($category->image && Storage::exists('public/' . $category->image)) {
            Storage::delete('public/' . $category->image);
        }
        
        return $this->categoryRepository->deleteById($id);
    }
}