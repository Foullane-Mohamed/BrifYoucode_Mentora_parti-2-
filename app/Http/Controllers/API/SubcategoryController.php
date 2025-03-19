<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subcategory\StoreSubcategoryRequest;
use App\Http\Requests\Subcategory\UpdateSubcategoryRequest;
use App\Services\SubcategoryService;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
    /**
     * @var SubcategoryService
     */
    protected $subcategoryService;

    /**
     * SubcategoryController constructor.
     * 
     * @param SubcategoryService $subcategoryService
     */
    public function __construct(SubcategoryService $subcategoryService)
    {
        $this->subcategoryService = $subcategoryService;
        $this->middleware('permission:subcategory.view')->only(['index', 'show', 'getByCategory', 'getWithCourses']);
        $this->middleware('permission:subcategory.create')->only(['store']);
        $this->middleware('permission:subcategory.edit')->only(['update']);
        $this->middleware('permission:subcategory.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the subcategories.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $subcategories = $this->subcategoryService->getAllSubcategories();
            
            return response()->json([
                'subcategories' => $subcategories,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching subcategories',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created subcategory in storage.
     * 
     * @param \App\Http\Requests\Subcategory\StoreSubcategoryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreSubcategoryRequest $request)
    {
        try {
            $data = $request->validated();
            
            $subcategory = $this->subcategoryService->createSubcategory($data);
            
            return response()->json([
                'message' => 'Subcategory created successfully',
                'subcategory' => $subcategory,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating subcategory',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified subcategory.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $subcategory = $this->subcategoryService->getSubcategoryById($id);
            
            return response()->json([
                'subcategory' => $subcategory,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching subcategory',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified subcategory in storage.
     * 
     * @param \App\Http\Requests\Subcategory\UpdateSubcategoryRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateSubcategoryRequest $request, $id)
    {
        try {
            $data = $request->validated();
            
            $result = $this->subcategoryService->updateSubcategory($id, $data);
            
            if ($result) {
                return response()->json([
                    'message' => 'Subcategory updated successfully',
                    'subcategory' => $this->subcategoryService->getSubcategoryById($id),
                ]);
            } else {
                return response()->json([
                    'message' => 'Error updating subcategory',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating subcategory',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified subcategory from storage.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $result = $this->subcategoryService->deleteSubcategory($id);
            
            if ($result) {
                return response()->json([
                    'message' => 'Subcategory deleted successfully',
                ]);
            } else {
                return response()->json([
                    'message' => 'Error deleting subcategory',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting subcategory',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get active subcategories.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActiveSubcategories()
    {
        try {
            $subcategories = $this->subcategoryService->getActiveSubcategories();
            
            return response()->json([
                'subcategories' => $subcategories,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching active subcategories',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get subcategory by slug.
     * 
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBySlug($slug)
    {
        try {
            $subcategory = $this->subcategoryService->getSubcategoryBySlug($slug);
            
            if (!$subcategory) {
                return response()->json([
                    'message' => 'Subcategory not found',
                ], 404);
            }
            
            return response()->json([
                'subcategory' => $subcategory,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching subcategory',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get subcategories by category.
     * 
     * @param int $categoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByCategory($categoryId)
    {
        try {
            $subcategories = $this->subcategoryService->getSubcategoriesByCategory($categoryId);
            
            return response()->json([
                'subcategories' => $subcategories,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching subcategories by category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get subcategory with courses.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWithCourses($id)
    {
        try {
            $subcategory = $this->subcategoryService->getSubcategoryWithCourses($id);
            
            return response()->json([
                'subcategory' => $subcategory,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching subcategory with courses',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}