<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tag\StoreTagRequest;
use App\Http\Requests\Tag\UpdateTagRequest;
use App\Services\TagService;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * @var TagService
     */
    protected $tagService;

    /**
     * TagController constructor.
     * 
     * @param TagService $tagService
     */
    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
        $this->middleware('permission:tag.view')->only(['index', 'show', 'getPopular', 'getWithCourses']);
        $this->middleware('permission:tag.create')->only(['store']);
        $this->middleware('permission:tag.edit')->only(['update']);
        $this->middleware('permission:tag.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the tags.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $tags = $this->tagService->getAllTags();
            
            return response()->json([
                'tags' => $tags,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching tags',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created tag in storage.
     * 
     * @param \App\Http\Requests\Tag\StoreTagRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreTagRequest $request)
    {
        try {
            $data = $request->validated();
            
            $tag = $this->tagService->createTag($data);
            
            return response()->json([
                'message' => 'Tag created successfully',
                'tag' => $tag,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating tag',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified tag.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $tag = $this->tagService->getTagById($id);
            
            return response()->json([
                'tag' => $tag,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching tag',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified tag in storage.
     * 
     * @param \App\Http\Requests\Tag\UpdateTagRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateTagRequest $request, $id)
    {
        try {
            $data = $request->validated();
            
            $result = $this->tagService->updateTag($id, $data);
            
            if ($result) {
                return response()->json([
                    'message' => 'Tag updated successfully',
                    'tag' => $this->tagService->getTagById($id),
                ]);
            } else {
                return response()->json([
                    'message' => 'Error updating tag',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating tag',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified tag from storage.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $result = $this->tagService->deleteTag($id);
            
            if ($result) {
                return response()->json([
                    'message' => 'Tag deleted successfully',
                ]);
            } else {
                return response()->json([
                    'message' => 'Error deleting tag',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting tag',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get tag by slug.
     * 
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBySlug($slug)
    {
        try {
            $tag = $this->tagService->getTagBySlug($slug);
            
            if (!$tag) {
                return response()->json([
                    'message' => 'Tag not found',
                ], 404);
            }
            
            return response()->json([
                'tag' => $tag,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching tag',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get popular tags.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPopular(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);
            
            $tags = $this->tagService->getPopularTags($limit);
            
            return response()->json([
                'tags' => $tags,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching popular tags',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get tag with courses.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWithCourses($id)
    {
        try {
            $tag = $this->tagService->getTagWithCourses($id);
            
            return response()->json([
                'tag' => $tag,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching tag with courses',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sync tags with course.
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $courseId
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncWithCourse(Request $request, $courseId)
    {
        try {
            $tagIds = $request->get('tags', []);
            
            $result = $this->tagService->syncTagsWithCourse($courseId, $tagIds);
            
            if ($result) {
                return response()->json([
                    'message' => 'Tags synced with course successfully',
                ]);
            } else {
                return response()->json([
                    'message' => 'Error syncing tags with course',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error syncing tags with course',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}