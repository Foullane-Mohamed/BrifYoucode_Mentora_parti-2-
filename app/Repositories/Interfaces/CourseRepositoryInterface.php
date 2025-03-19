<?php

namespace App\Repositories\Interfaces;

use App\Models\Course;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CourseRepositoryInterface extends EloquentRepositoryInterface
{
    /**
     * Get published courses.
     * 
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPublishedCourses(int $perPage = 10): LengthAwarePaginator;

    /**
     * Get courses by mentor.
     * 
     * @param int $mentorId
     * @return Collection
     */
    public function getCoursesByMentor(int $mentorId): Collection;

    /**
     * Get course by slug.
     * 
     * @param string $slug
     * @return Course|null
     */
    public function getCourseBySlug(string $slug): ?Course;

    /**
     * Get course with videos.
     * 
     * @param int $courseId
     * @return Course|null
     */
    public function getCourseWithVideos(int $courseId): ?Course;

    /**
     * Get course with tags.
     * 
     * @param int $courseId
     * @return Course|null
     */
    public function getCourseWithTags(int $courseId): ?Course;

    /**
     * Get course with enrollments.
     * 
     * @param int $courseId
     * @return Course|null
     */
    public function getCourseWithEnrollments(int $courseId): ?Course;

    /**
     * Publish course.
     * 
     * @param int $courseId
     * @return bool
     */
    public function publishCourse(int $courseId): bool;

    /**
     * Unpublish course.
     * 
     * @param int $courseId
     * @return bool
     */
    public function unpublishCourse(int $courseId): bool;

    /**
     * Search courses.
     * 
     * @param string $query
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function searchCourses(string $query, int $perPage = 10): LengthAwarePaginator;
}