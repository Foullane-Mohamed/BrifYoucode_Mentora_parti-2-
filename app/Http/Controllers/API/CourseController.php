<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Course\StoreCourseRequest;
use App\Http\Requests\Course\UpdateCourseRequest;
use App\Services\CourseService;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * @var CourseService
     */
    protected $courseService;

    /**
     * CourseController constructor.
     * 
     * @param CourseService $courseService
     */
    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
        $this->middleware('permission:course.view')->only(['index', 'show', 'getPublished', 'getByMentor', 'getWithVideos', 'getWithTags', 'getWithEnrollments']);
        $this->middleware('permission:course.create')->only(['store']);
        $this->middleware('permission:course.edit')->only(['update']);
        $this->middleware('permission:course.delete')->only(['destroy']);
        $this->middleware('permission:course.publish')->only(['publish', 'unpublish']);
    }

    /**
     * Display a listing of the courses.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $courses = $this->courseService->getAllCourses();
            
            return response()->json([
                'courses' => $courses,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching courses',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created course in storage.
     * 
     * @param \App\Http\Requests\Course\StoreCourseRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreCourseRequest $request)
    {
        try {
            $data = $request->validated();
            
            // Set the authenticated user as the course owner (mentor)
            $data['user_id'] = auth()->id();
            
            $course = $this->courseService->createCourse($data);
            
            return response()->json([
                'message' => 'Course created successfully',
                'course' => $course,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating course',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified course.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $course = $this->courseService->getCourseById($id);
            
            return response()->json([
                'course' => $course,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching course',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified course in storage.
     * 
     * @param \App\Http\Requests\Course\UpdateCourseRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCourseRequest $request, $id)
    {
        try {
            $data = $request->validated();
            
            // Check if the user is the course owner or an admin
            $course = $this->courseService->getCourseById($id);
            
            if ($course->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return response()->json([
                    'message' => 'Unauthorized. You can only update your own courses.',
                ], 403);
            }
            
            $result = $this->courseService->updateCourse($id, $data);
            
            if ($result) {
                return response()->json([
                    'message' => 'Course updated successfully',
                    'course' => $this->courseService->getCourseById($id),
                ]);
            } else {
                return response()->json([
                    'message' => 'Error updating course',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating course',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified course from storage.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            // Check if the user is the course owner or an admin
            $course = $this->courseService->getCourseById($id);
            
            if ($course->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return response()->json([
                    'message' => 'Unauthorized. You can only delete your own courses.',
                ], 403);
            }
            
            $result = $this->courseService->deleteCourse($id);
            
            if ($result) {
                return response()->json([
                    'message' => 'Course deleted successfully',
                ]);
            } else {
                return response()->json([
                    'message' => 'Error deleting course',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting course',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get published courses.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPublished(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            
            $courses = $this->courseService->getPublishedCourses($perPage);
            
            return response()->json([
                'courses' => $courses,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching published courses',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get courses by mentor.
     * 
     * @param int $mentorId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByMentor($mentorId)
    {
        try {
            $courses = $this->courseService->getCoursesByMentor($mentorId);
            
            return response()->json([
                'courses' => $courses,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching courses by mentor',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get course by slug.
     * 
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBySlug($slug)
    {
        try {
            $course = $this->courseService->getCourseBySlug($slug);
            
            if (!$course) {
                return response()->json([
                    'message' => 'Course not found',
                ], 404);
            }
            
            return response()->json([
                'course' => $course,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching course',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get course with videos.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWithVideos($id)
    {
        try {
            $course = $this->courseService->getCourseWithVideos($id);
            
            return response()->json([
                'course' => $course,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching course with videos',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get course with tags.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWithTags($id)
    {
        try {
            $course = $this->courseService->getCourseWithTags($id);
            
            return response()->json([
                'course' => $course,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching course with tags',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get course with enrollments.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWithEnrollments($id)
    {
        try {
            $course = $this->courseService->getCourseWithEnrollments($id);
            
            return response()->json([
                'course' => $course,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching course with enrollments',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Publish course.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function publish($id)
    {
        try {
            // Check if the user is the course owner or an admin
            $course = $this->courseService->getCourseById($id);
            
            if ($course->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return response()->json([
                    'message' => 'Unauthorized. You can only publish your own courses.',
                ], 403);
            }
            
            $result = $this->courseService->publishCourse($id);
            
            if ($result) {
                return response()->json([
                    'message' => 'Course published successfully',
                ]);
            } else {
                return response()->json([
                    'message' => 'Error publishing course',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error publishing course',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Unpublish course.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function unpublish($id)
    {
        try {
            // Check if the user is the course owner or an admin
            $course = $this->courseService->getCourseById($id);
            
            if ($course->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return response()->json([
                    'message' => 'Unauthorized. You can only unpublish your own courses.',
                ], 403);
            }
            
            $result = $this->courseService->unpublishCourse($id);
            
            if ($result) {
                return response()->json([
                    'message' => 'Course unpublished successfully',
                ]);
            } else {
                return response()->json([
                    'message' => 'Error unpublishing course',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error unpublishing course',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search courses.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('q', '');
            $perPage = $request->get('per_page', 10);
            
            if (empty($query)) {
                return response()->json([
                    'message' => 'Search query is required',
                ], 400);
            }
            
            $courses = $this->courseService->searchCourses($query, $perPage);
            
            return response()->json([
                'courses' => $courses,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error searching courses',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}