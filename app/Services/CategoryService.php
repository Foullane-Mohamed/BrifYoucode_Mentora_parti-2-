<?php

namespace App\Services;

use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Support\Str;

class CategoryService
{
    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAllCategories()
    {
        return $this->categoryRepository->all();
    }

    public function getCategoryById($id)
    {
        return $this->categoryRepository->find($id);
    }

    public function getCategoryWithSubcategories($id)
    {
        return $this->categoryRepository->getWithSubcategories($id);
    }

    public function getCategoryWithCourses($id)
    {
        return $this->categoryRepository->getWithCourses($id);
    }

    public function createCategory(array $data)
    {
        // Generate slug from name
        $data['slug'] = Str::slug($data['name']);
        
        return $this->categoryRepository->create($data);
    }

    public function updateCategory($id, array $data)
    {
        // Update slug if name is changed
        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        
        return $this->categoryRepository->update($id, $data);
    }

    public function deleteCategory($id)
    {
        return $this->categoryRepository->delete($id);
    }
}