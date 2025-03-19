<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Repositories\Interfaces\EnrollmentRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\CourseRepositoryInterface;

class EnrollmentService
{
    /**
     * @var EnrollmentRepositoryInterface
     */
    protected $enrollmentRepository;

    /**
     * @var UserRepositoryInterface
     */
    protected $userRepository;

    /**
     * @var CourseRepositoryInterface
     */
    protected $courseRepository;

    /**
     * EnrollmentService constructor.
     * 
     * @param EnrollmentRepositoryInterface $enrollmentRepository
     * @param UserRepositoryInterface $userRepository
     * @param CourseRepositoryInterface $courseRepository
     */
    public function __construct(
        EnrollmentRepositoryInterface $enrollmentRepository,
        UserRepositoryInterface $userRepository,
        CourseRepositoryInterface $courseRepository
    ) {
        $this->enrollmentRepository = $enrollmentRepository;
        $this->userRepository = $userRepository;
        $this->courseRepository = $courseRepository;
    }

    /**
     * Get all enrollments.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllEnrollments()
    {
        return $this->enrollmentRepository->all(['*'], ['user', 'course']);
    }

    /**
     * Get enrollments by user.
     * 
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEnrollmentsByUser($userId)
    {
        return $this->enrollmentRepository->getEnrollmentsByUser($userId);
    }

    /**
     * Get enrollments by course.
     * 
     * @param int $courseId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEnrollmentsByCourse($courseId)
    {
        return $this->enrollmentRepository->getEnrollmentsByCourse($courseId);
    }

    /**
     * Get enrollments by status.
     * 
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEnrollmentsByStatus($status)
    {
        return $this->enrollmentRepository->getEnrollmentsByStatus($status);
    }

    /**
     * Get enrollment by ID.
     * 
     * @param int $id
     * @return Enrollment
     */
    public function getEnrollmentById($id)
    {
        return $this->enrollmentRepository->findById($id, ['*'], ['user', 'course']);
    }

    /**
     * Create new enrollment.
     * 
     * @param array $data
     * @return Enrollment
     */
    public function createEnrollment(array $data)
    {
        // Check if user exists
        $this->userRepository->findById($data['user_id']);
        
        // Check if course exists
        $this->courseRepository->findById($data['course_id']);
        
        // Check if enrollment already exists
        $existingEnrollment = $this->enrollmentRepository->getEnrollmentByUserAndCourse($data['user_id'], $data['course_id']);
        if ($existingEnrollment) {
            throw new \Exception('User is already enrolled in this course');
        }
        
        // Default status is pending if not provided
        if (!isset($data['status'])) {
            $data['status'] = 'pending';
        }
        
        // Default progress is 0 if not provided
        if (!isset($data['progress'])) {
            $data['progress'] = 0;
        }
        
        return $this->enrollmentRepository->create($data);
    }

    /**
     * Update enrollment status.
     * 
     * @param int $enrollmentId
     * @param string $status
     * @return bool
     */
    public function updateEnrollmentStatus($enrollmentId, $status)
    {
        return $this->enrollmentRepository->updateEnrollmentStatus($enrollmentId, $status);
    }

    /**
     * Update enrollment progress.
     * 
     * @param int $enrollmentId
     * @param float $progress
     * @return bool
     */
    public function updateEnrollmentProgress($enrollmentId, $progress)
    {
        return $this->enrollmentRepository->updateEnrollmentProgress($enrollmentId, $progress);
    }

    /**
     * Complete enrollment.
     * 
     * @param int $enrollmentId
     * @return bool
     */
    public function completeEnrollment($enrollmentId)
    {
        return $this->enrollmentRepository->completeEnrollment($enrollmentId);
    }

    /**
     * Delete enrollment.
     * 
     * @param int $id
     * @return bool
     */
    public function deleteEnrollment($id)
    {
        return $this->enrollmentRepository->deleteById($id);
    }

    /**
     * Approve enrollment.
     * 
     * @param int $enrollmentId
     * @return bool
     */
    public function approveEnrollment($enrollmentId)
    {
        return $this->enrollmentRepository->updateEnrollmentStatus($enrollmentId, 'approved');
    }

    /**
     * Reject enrollment.
     * 
     * @param int $enrollmentId
     * @return bool
     */
    public function rejectEnrollment($enrollmentId)
    {
        return $this->enrollmentRepository->updateEnrollmentStatus($enrollmentId, 'rejected');
    }

    /**
     * Get pending enrollments.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPendingEnrollments()
    {
        return $this->enrollmentRepository->getEnrollmentsByStatus('pending');
    }
}