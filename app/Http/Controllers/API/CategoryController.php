<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * @var CategoryService
     */
    protected $categoryService;

    /**
     * CategoryController constructor.
     * 
     * @param CategoryService $categoryService
     */
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
        $this->middleware('permission:category.view')->only(['index', 'show', 'getWithSubcategories', 'getWithCourses']);
        $this->middleware('permission:category.create')->only(['store']);
        $this->middleware('permission:category.edit')->only(['update']);
        $this->middleware('permission:category.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the categories.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $categories = $this->categoryService->getAllCategories();
            
            return response()->json([
                'categories' => $categories,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching categories',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created category in storage.
     * 
     * @param \App\Http\Requests\Category\StoreCategoryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreCategoryRequest $request)
    {
        try {
            $data = $request->validated();
            
            $category = $this->categoryService->createCategory($data);
            
            return response()->json([
                'message' => 'Category created successfully',
                'category' => $category,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified category.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $category = $this->categoryService->getCategoryById($id);
            
            return response()->json([
                'category' => $category,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified category in storage.
     * 
     * @param \App\Http\Requests\Category\UpdateCategoryRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCategoryRequest $request, $id)
    {
        try {
            $data = $request->validated();
            
            $result = $this->categoryService->updateCategory($id, $data);
            
            if ($result) {
                return response()->json([
                    'message' => 'Category updated successfully',
                    'category' => $this->categoryService->getCategoryById($id),
                ]);
            } else {
                return response()->json([
                    'message' => 'Error updating category',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified category from storage.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $result = $this->categoryService->deleteCategory($id);
            
            if ($result) {
                return response()->json([
                    'message' => 'Category deleted successfully',
                ]);
            } else {
                return response()->json([
                    'message' => 'Error deleting category',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get active categories.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActiveCategories()
    {
        try {
            $categories = $this->categoryService->getActiveCategories();
            
            return response()->json([
                'categories' => $categories,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching active categories',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get category by slug.
     * 
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBySlug($slug)
    {
        try {
            $category = $this->categoryService->getCategoryBySlug($slug);
            
            if (!$category) {
                return response()->json([
                    'message' => 'Category not found',
                ], 404);
            }
            
            return response()->json([
                'category' => $category,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get categories with subcategories.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWithSubcategories()
    {
        try {
            $categories = $this->categoryService->getCategoriesWithSubcategories();
            
            return response()->json([
                'categories' => $categories,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching categories with subcategories',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get category with courses.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWithCourses($id)
    {
        try {
            $category = $this->categoryService->getCategoryWithCourses($id);
            
            return response()->json([
                'category' => $category,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching category with courses',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}