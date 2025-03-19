<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Video\StoreVideoRequest;
use App\Http\Requests\Video\UpdateVideoRequest;
use App\Services\VideoService;
use App\Services\CourseService;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    /**
     * @var VideoService
     */
    protected $videoService;

    /**
     * @var CourseService
     */
    protected $courseService;

    /**
     * VideoController constructor.
     * 
     * @param VideoService $videoService
     * @param CourseService $courseService
     */
    public function __construct(VideoService $videoService, CourseService $courseService)
    {
        $this->videoService = $videoService;
        $this->courseService = $courseService;
        $this->middleware('permission:video.view')->only(['index', 'show', 'getByCourse']);
        $this->middleware('permission:video.create')->only(['store']);
        $this->middleware('permission:video.edit')->only(['update', 'updateOrder', 'reorder']);
        $this->middleware('permission:video.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the videos.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $videos = $this->videoService->getAllVideos();
            
            return response()->json([
                'videos' => $videos,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching videos',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created video in storage.
     * 
     * @param \App\Http\Requests\Video\StoreVideoRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreVideoRequest $request)
    {
        try {
            $data = $request->validated();
            
            // Check if the user is the course owner or an admin
            $course = $this->courseService->getCourseById($data['course_id']);
            
            if ($course->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return response()->json([
                    'message' => 'Unauthorized. You can only add videos to your own courses.',
                ], 403);
            }
            
            $video = $this->videoService->createVideo($data);
            
            return response()->json([
                'message' => 'Video created successfully',
                'video' => $video,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating video',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified video.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $video = $this->videoService->getVideoById($id);
            
            return response()->json([
                'video' => $video,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching video',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified video in storage.
     * 
     * @param \App\Http\Requests\Video\UpdateVideoRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateVideoRequest $request, $id)
    {
        try {
            $data = $request->validated();
            
            // Check if the user is the course owner or an admin
            $video = $this->videoService->getVideoById($id);
            $course = $this->courseService->getCourseById($video->course_id);
            
            if ($course->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return response()->json([
                    'message' => 'Unauthorized. You can only update videos in your own courses.',
                ], 403);
            }
            
            $result = $this->videoService->updateVideo($id, $data);
            
            if ($result) {
                return response()->json([
                    'message' => 'Video updated successfully',
                    'video' => $this->videoService->getVideoById($id),
                ]);
            } else {
                return response()->json([
                    'message' => 'Error updating video',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating video',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified video from storage.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            // Check if the user is the course owner or an admin
            $video = $this->videoService->getVideoById($id);
            $course = $this->courseService->getCourseById($video->course_id);
            
            if ($course->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return response()->json([
                    'message' => 'Unauthorized. You can only delete videos in your own courses.',
                ], 403);
            }
            
            $result = $this->videoService->deleteVideo($id);
            
            if ($result) {
                return response()->json([
                    'message' => 'Video deleted successfully',
                ]);
            } else {
                return response()->json([
                    'message' => 'Error deleting video',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting video',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get videos by course.
     * 
     * @param int $courseId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByCourse($courseId)
    {
        try {
            $videos = $this->videoService->getVideosByCourse($courseId);
            
            return response()->json([
                'videos' => $videos,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching videos by course',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update video order.
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOrder(Request $request, $id)
    {
        try {
            $order = $request->get('order');
            
            if (!is_numeric($order)) {
                return response()->json([
                    'message' => 'Invalid order value',
                ], 400);
            }
            
            // Check if the user is the course owner or an admin
            $video = $this->videoService->getVideoById($id);
            $course = $this->courseService->getCourseById($video->course_id);
            
            if ($course->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return response()->json([
                    'message' => 'Unauthorized. You can only update videos in your own courses.',
                ], 403);
            }
            
            $result = $this->videoService->updateVideoOrder($id, $order);
            
            if ($result) {
                return response()->json([
                    'message' => 'Video order updated successfully',
                ]);
            } else {
                return response()->json([
                    'message' => 'Error updating video order',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating video order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reorder videos.
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $courseId
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorder(Request $request, $courseId)
    {
        try {
            $videoOrders = $request->get('videos', []);
            
            if (!is_array($videoOrders) || count($videoOrders) === 0) {
                return response()->json([
                    'message' => 'Invalid videos order data',
                ], 400);
            }
            
            // Check if the user is the course owner or an admin
            $course = $this->courseService->getCourseById($courseId);
            
            if ($course->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return response()->json([
                    'message' => 'Unauthorized. You can only reorder videos in your own courses.',
                ], 403);
            }
            
            $result = $this->videoService->reorderVideos($courseId, $videoOrders);
            
            if ($result) {
                return response()->json([
                    'message' => 'Videos reordered successfully',
                ]);
            } else {
                return response()->json([
                    'message' => 'Error reordering videos',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error reordering videos',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}