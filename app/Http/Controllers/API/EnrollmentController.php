<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Enrollment\StoreEnrollmentRequest;
use App\Http\Requests\Enrollment\UpdateEnrollmentRequest;
use App\Services\EnrollmentService;
use App\Services\CourseService;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    /**
     * @var EnrollmentService
     */
    protected $enrollmentService;

    /**
     * @var CourseService
     */
    protected $courseService;

    /**
     * EnrollmentController constructor.
     * 
     * @param EnrollmentService $enrollmentService
     * @param CourseService $courseService
     */
    public function __construct(EnrollmentService $enrollmentService, CourseService $courseService)
    {
        $this->enrollmentService = $enrollmentService;
        $this->courseService = $courseService;
        $this->middleware('permission:enrollment.view')->only(['index', 'show', 'getByUser', 'getByCourse', 'getByStatus']);
        $this->middleware('permission:enrollment.create')->only(['store']);
        $this->middleware('permission:enrollment.edit')->only(['update', 'updateProgress']);
        $this->middleware('permission:enrollment.delete')->only(['destroy']);
        $this->middleware('permission:enrollment.approve')->only(['approve', 'reject']);
    }

    /**
     * Display a listing of the enrollments.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $enrollments = $this->enrollmentService->getAllEnrollments();
            
            return response()->json([
                'enrollments' => $enrollments,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching enrollments',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created enrollment in storage.
     * 
     * @param \App\Http\Requests\Enrollment\StoreEnrollmentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreEnrollmentRequest $request)
    {
        try {
            $data = $request->validated();
            
            // Ensure the course exists and is published
            $course = $this->courseService->getCourseById($data['course_id']);
            
            if (!$course->is_published) {
                return response()->json([
                    'message' => 'Cannot enroll in an unpublished course',
                ], 400);
            }
            
            $enrollment = $this->enrollmentService->createEnrollment($data);
            
            return response()->json([
                'message' => 'Enrollment created successfully',
                'enrollment' => $enrollment,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating enrollment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified enrollment.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $enrollment = $this->enrollmentService->getEnrollmentById($id);
            
            return response()->json([
                'enrollment' => $enrollment,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching enrollment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified enrollment in storage.
     * 
     * @param \App\Http\Requests\Enrollment\UpdateEnrollmentRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateEnrollmentRequest $request, $id)
    {
        try {
            $data = $request->validated();
            
            // Get the enrollment
            $enrollment = $this->enrollmentService->getEnrollmentById($id);
            
            // If this is a status update, check if the user is the course mentor or an admin
            if (isset($data['status'])) {
                $course = $this->courseService->getCourseById($enrollment->course_id);
                
                if ($course->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                    return response()->json([
                        'message' => 'Unauthorized. Only the course mentor or an admin can update enrollment status.',
                    ], 403);
                }
            }
            
            // If this is a progress update, check if the user is the enrolled student
            if (isset($data['progress']) || isset($data['completed_at'])) {
                if ($enrollment->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                    return response()->json([
                        'message' => 'Unauthorized. Only the enrolled student or an admin can update enrollment progress.',
                    ], 403);
                }
            }
            
            $result = $this->enrollmentService->updateEnrollment($id, $data);
            
            if ($result) {
                return response()->json([
                    'message' => 'Enrollment updated successfully',
                    'enrollment' => $this->enrollmentService->getEnrollmentById($id),
                ]);
            } else {
                return response()->json([
                    'message' => 'Error updating enrollment',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating enrollment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified enrollment from storage.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            // Get the enrollment
            $enrollment = $this->enrollmentService->getEnrollmentById($id);
            
            // Check if the user is the enrolled student, the course mentor, or an admin
            $course = $this->courseService->getCourseById($enrollment->course_id);
            
            if ($enrollment->user_id !== auth()->id() && $course->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return response()->json([
                    'message' => 'Unauthorized. You can only delete your own enrollments or enrollments in your courses.',
                ], 403);
            }
            
            $result = $this->enrollmentService->deleteEnrollment($id);
            
            if ($result) {
                return response()->json([
                    'message' => 'Enrollment deleted successfully',
                ]);
            } else {
                return response()->json([
                    'message' => 'Error deleting enrollment',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting enrollment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get enrollments by user.
     * 
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByUser($userId)
    {
        try {
            // Check if the user is requesting their own enrollments or is an admin
            if ($userId != auth()->id() && !auth()->user()->hasRole('admin')) {
                return response()->json([
                    'message' => 'Unauthorized. You can only view your own enrollments.',
                ], 403);
            }
            
            $enrollments = $this->enrollmentService->getEnrollmentsByUser($userId);
            
            return response()->json([
                'enrollments' => $enrollments,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching enrollments by user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get enrollments by course.
     * 
     * @param int $courseId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByCourse($courseId)
    {
        try {
            // Check if the user is the course mentor or an admin
            $course = $this->courseService->getCourseById($courseId);
            
            if ($course->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return response()->json([
                    'message' => 'Unauthorized. You can only view enrollments in your own courses.',
                ], 403);
            }
            
            $enrollments = $this->enrollmentService->getEnrollmentsByCourse($courseId);
            
            return response()->json([
                'enrollments' => $enrollments,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching enrollments by course',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get enrollments by status.
     * 
     * @param string $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByStatus($status)
    {
        try {
            // Validate status
            if (!in_array($status, ['pending', 'approved', 'rejected'])) {
                return response()->json([
                    'message' => 'Invalid status',
                ], 400);
            }
            
            // Only admin and mentors can view enrollments by status
            if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('mentor')) {
                return response()->json([
                    'message' => 'Unauthorized. Only admin and mentors can view enrollments by status.',
                ], 403);
            }
            
            $enrollments = $this->enrollmentService->getEnrollmentsByStatus($status);
            
            // If mentor, filter to only show their courses
            if (auth()->user()->hasRole('mentor') && !auth()->user()->hasRole('admin')) {
                $mentorId = auth()->id();
                
                $enrollments = $enrollments->filter(function($enrollment) use ($mentorId) {
                    return $enrollment->course->user_id === $mentorId;
                });
            }
            
            return response()->json([
                'enrollments' => $enrollments,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching enrollments by status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Approve enrollment.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function approve($id)
    {
        try {
            // Get the enrollment
            $enrollment = $this->enrollmentService->getEnrollmentById($id);
            
            // Check if the user is the course mentor or an admin
            $course = $this->courseService->getCourseById($enrollment->course_id);
            
            if ($course->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return response()->json([
                    'message' => 'Unauthorized. Only the course mentor or an admin can approve enrollments.',
                ], 403);
            }
            
            $result = $this->enrollmentService->approveEnrollment($id);
            
            if ($result) {
                return response()->json([
                    'message' => 'Enrollment approved successfully',
                    'enrollment' => $this->enrollmentService->getEnrollmentById($id),
                ]);
            } else {
                return response()->json([
                    'message' => 'Error approving enrollment',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error approving enrollment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reject enrollment.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function reject($id)
    {
        try {
            // Get the enrollment
            $enrollment = $this->enrollmentService->getEnrollmentById($id);
            
            // Check if the user is the course mentor or an admin
            $course = $this->courseService->getCourseById($enrollment->course_id);
            
            if ($course->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return response()->json([
                    'message' => 'Unauthorized. Only the course mentor or an admin can reject enrollments.',
                ], 403);
            }
            
            $result = $this->enrollmentService->rejectEnrollment($id);
            
            if ($result) {
                return response()->json([
                    'message' => 'Enrollment rejected successfully',
                    'enrollment' => $this->enrollmentService->getEnrollmentById($id),
                ]);
            } else {
                return response()->json([
                    'message' => 'Error rejecting enrollment',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error rejecting enrollment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update enrollment progress.
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProgress(Request $request, $id)
    {
        try {
            $progress = $request->get('progress');
            
            if (!is_numeric($progress) || $progress < 0 || $progress > 100) {
                return response()->json([
                    'message' => 'Invalid progress value. Must be between 0 and 100.',
                ], 400);
            }
            
            // Get the enrollment
            $enrollment = $this->enrollmentService->getEnrollmentById($id);
            
            // Check if the user is the enrolled student or an admin
            if ($enrollment->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return response()->json([
                    'message' => 'Unauthorized. Only the enrolled student or an admin can update enrollment progress.',
                ], 403);
            }
            
            $result = $this->enrollmentService->updateEnrollmentProgress($id, $progress);
            
            if ($result) {
                return response()->json([
                    'message' => 'Enrollment progress updated successfully',
                    'enrollment' => $this->enrollmentService->getEnrollmentById($id),
                ]);
            } else {
                return response()->json([
                    'message' => 'Error updating enrollment progress',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating enrollment progress',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}