<?php

namespace App\Services;

use App\Models\Subcategory;
use App\Repositories\Interfaces\SubcategoryRepositoryInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SubcategoryService
{
    /**
     * @var SubcategoryRepositoryInterface
     */
    protected $subcategoryRepository;

    /**
     * SubcategoryService constructor.
     * 
     * @param SubcategoryRepositoryInterface $subcategoryRepository
     */
    public function __construct(SubcategoryRepositoryInterface $subcategoryRepository)
    {
        $this->subcategoryRepository = $subcategoryRepository;
    }

    /**
     * Get all subcategories.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllSubcategories()
    {
        return $this->subcategoryRepository->all(['*'], ['category']);
    }

    /**
     * Get active subcategories.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveSubcategories()
    {
        return $this->subcategoryRepository->getActiveSubcategories();
    }

    /**
     * Get subcategory by ID.
     * 
     * @param int $id
     * @return Subcategory
     */
    public function getSubcategoryById($id)
    {
        return $this->subcategoryRepository->findById($id, ['*'], ['category']);
    }

    /**
     * Get subcategory by slug.
     * 
     * @param string $slug
     * @return Subcategory|null
     */
    public function getSubcategoryBySlug($slug)
    {
        return $this->subcategoryRepository->getSubcategoryBySlug($slug);
    }

    /**
     * Get subcategories by category.
     * 
     * @param int $categoryId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSubcategoriesByCategory($categoryId)
    {
        return $this->subcategoryRepository->getSubcategoriesByCategory($categoryId);
    }

    /**
     * Get subcategory with courses.
     * 
     * @param int $subcategoryId
     * @return Subcategory|null
     */
    public function getSubcategoryWithCourses($subcategoryId)
    {
        return $this->subcategoryRepository->getSubcategoryWithCourses($subcategoryId);
    }

    /**
     * Create new subcategory.
     * 
     * @param array $data
     * @return Subcategory
     */
    public function createSubcategory(array $data)
    {
        // Generate slug
        $data['slug'] = Str::slug($data['name']);
        
        // Handle image if provided
        if (isset($data['image']) && $data['image']) {
            $image = $data['image'];
            $filename = Str::random(20) . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/subcategories', $filename);
            $data['image'] = 'subcategories/' . $filename;
        }
        
        // Default is_active to true if not provided
        if (!isset($data['is_active'])) {
            $data['is_active'] = true;
        }
        
        return $this->subcategoryRepository->create($data);
    }

    /**
     * Update subcategory.
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateSubcategory($id, array $data)
    {
        $subcategory = $this->subcategoryRepository->findById($id);
        
        // Generate slug if name is provided
        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        
        // Handle image if provided
        if (isset($data['image']) && $data['image']) {
            // Delete old image
            if ($subcategory->image && Storage::exists('public/' . $subcategory->image)) {
                Storage::delete('public/' . $subcategory->image);
            }
            
            $image = $data['image'];
            $filename = Str::random(20) . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/subcategories', $filename);
            $data['image'] = 'subcategories/' . $filename;
        }
        
        return $this->subcategoryRepository->update($id, $data);
    }

    /**
     * Delete subcategory.
     * 
     * @param int $id
     * @return bool
     */
    public function deleteSubcategory($id)
    {
        $subcategory = $this->subcategoryRepository->findById($id);
        
        // Delete image if exists
        if ($subcategory->image && Storage::exists('public/' . $subcategory->image)) {
            Storage::delete('public/' . $subcategory->image);
        }
        
        return $this->subcategoryRepository->deleteById($id);
    }
}