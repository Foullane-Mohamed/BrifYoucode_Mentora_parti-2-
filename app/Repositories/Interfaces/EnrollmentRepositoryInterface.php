<?php

namespace App\Repositories\Interfaces;

use App\Models\Enrollment;
use Illuminate\Database\Eloquent\Collection;

interface EnrollmentRepositoryInterface extends EloquentRepositoryInterface
{
    /**
     * Get enrollments by user.
     * 
     * @param int $userId
     * @return Collection
     */
    public function getEnrollmentsByUser(int $userId): Collection;

    /**
     * Get enrollments by course.
     * 
     * @param int $courseId
     * @return Collection
     */
    public function getEnrollmentsByCourse(int $courseId): Collection;

    /**
     * Get enrollments by status.
     * 
     * @param string $status
     * @return Collection
     */
    public function getEnrollmentsByStatus(string $status): Collection;

    /**
     * Get enrollment by user and course.
     * 
     * @param int $userId
     * @param int $courseId
     * @return Enrollment|null
     */
    public function getEnrollmentByUserAndCourse(int $userId, int $courseId): ?Enrollment;

    /**
     * Update enrollment status.
     * 
     * @param int $enrollmentId
     * @param string $status
     * @return bool
     */
    public function updateEnrollmentStatus(int $enrollmentId, string $status): bool;

    /**
     * Update enrollment progress.
     * 
     * @param int $enrollmentId
     * @param float $progress
     * @return bool
     */
    public function updateEnrollmentProgress(int $enrollmentId, float $progress): bool;

    /**
     * Complete enrollment.
     * 
     * @param int $enrollmentId
     * @return bool
     */
    public function completeEnrollment(int $enrollmentId): bool;
}