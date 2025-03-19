<?php

namespace App\Services;

use App\Models\Course;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use App\Repositories\Interfaces\TagRepositoryInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CourseService
{
    /**
     * @var CourseRepositoryInterface
     */
    protected $courseRepository;

    /**
     * @var TagRepositoryInterface
     */
    protected $tagRepository;

    /**
     * CourseService constructor.
     * 
     * @param CourseRepositoryInterface $courseRepository
     * @param TagRepositoryInterface $tagRepository
     */
    public function __construct(
        CourseRepositoryInterface $courseRepository,
        TagRepositoryInterface $tagRepository
    ) {
        $this->courseRepository = $courseRepository;
        $this->tagRepository = $tagRepository;
    }

    /**
     * Get all courses.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllCourses()
    {
        return $this->courseRepository->all(['*'], ['user', 'subcategory.category', 'tags']);
    }

    /**
     * Get published courses.
     * 
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPublishedCourses($perPage = 10)
    {
        return $this->courseRepository->getPublishedCourses($perPage);
    }

    /**
     * Get courses by mentor.
     * 
     * @param int $mentorId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCoursesByMentor($mentorId)
    {
        return $this->courseRepository->getCoursesByMentor($mentorId);
    }

    /**
     * Get course by ID.
     * 
     * @param int $id
     * @return Course
     */
    public function getCourseById($id)
    {
        return $this->courseRepository->findById($id, ['*'], ['user', 'subcategory.category', 'tags', 'videos']);
    }

    /**
     * Get course by slug.
     * 
     * @param string $slug
     * @return Course|null
     */
    public function getCourseBySlug($slug)
    {
        return $this->courseRepository->getCourseBySlug($slug);
    }

    /**
     * Get course with videos.
     * 
     * @param int $courseId
     * @return Course|null
     */
    public function getCourseWithVideos($courseId)
    {
        return $this->courseRepository->getCourseWithVideos($courseId);
    }

    /**
     * Get course with tags.
     * 
     * @param int $courseId
     * @return Course|null
     */
    public function getCourseWithTags($courseId)
    {
        return $this->courseRepository->getCourseWithTags($courseId);
    }

    /**
     * Get course with enrollments.
     * 
     * @param int $courseId
     * @return Course|null
     */
    public function getCourseWithEnrollments($courseId)
    {
        return $this->courseRepository->getCourseWithEnrollments($courseId);
    }

    /**
     * Create new course.
     * 
     * @param array $data
     * @return Course
     */
    public function createCourse(array $data)
    {
        // Generate slug
        $data['slug'] = Str::slug($data['title']);
        
        // Handle thumbnail if provided
        if (isset($data['thumbnail']) && $data['thumbnail']) {
            $thumbnail = $data['thumbnail'];
            $filename = Str::random(20) . '.' . $thumbnail->getClientOriginalExtension();
            $thumbnail->storeAs('public/courses', $filename);
            $data['thumbnail'] = 'courses/' . $filename;
        }
        
        // By default, course is not published
        if (!isset($data['is_published'])) {
            $data['is_published'] = false;
        }
        
        // Create course
        $course = $this->courseRepository->create($data);
        
        // Sync tags if provided
        if (isset($data['tags']) && is_array($data['tags'])) {
            $this->tagRepository->syncWithCourse($course->id, $data['tags']);
        }
        
        return $course;
    }

    /**
     * Update course.
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateCourse($id, array $data)
    {
        $course = $this->courseRepository->findById($id);
        
        // Generate slug if title is provided
        if (isset($data['title'])) {
            $data['slug'] = Str::slug($data['title']);
        }
        
        // Handle thumbnail if provided
        if (isset($data['thumbnail']) && $data['thumbnail']) {
            // Delete old thumbnail
            if ($course->thumbnail && Storage::exists('public/' . $course->thumbnail)) {
                Storage::delete('public/' . $course->thumbnail);
            }
            
            $thumbnail = $data['thumbnail'];
            $filename = Str::random(20) . '.' . $thumbnail->getClientOriginalExtension();
            $thumbnail->storeAs('public/courses', $filename);
            $data['thumbnail'] = 'courses/' . $filename;
        }
        
        // Update course
        $result = $this->courseRepository->update($id, $data);
        
        // Sync tags if provided
        if (isset($data['tags']) && is_array($data['tags'])) {
            $this->tagRepository->syncWithCourse($id, $data['tags']);
        }
        
        return $result;
    }

    /**
     * Delete course.
     * 
     * @param int $id
     * @return bool
     */
    public function deleteCourse($id)
    {
        $course = $this->courseRepository->findById($id);
        
        // Delete thumbnail if exists
        if ($course->thumbnail && Storage::exists('public/' . $course->thumbnail)) {
            Storage::delete('public/' . $course->thumbnail);
        }
        
        return $this->courseRepository->deleteById($id);
    }

    /**
     * Publish course.
     * 
     * @param int $courseId
     * @return bool
     */
    public function publishCourse($courseId)
    {
        return $this->courseRepository->publishCourse($courseId);
    }

    /**
     * Unpublish course.
     * 
     * @param int $courseId
     * @return bool
     */
    public function unpublishCourse($courseId)
    {
        return $this->courseRepository->unpublishCourse($courseId);
    }

    /**
     * Search courses.
     * 
     * @param string $query
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function searchCourses($query, $perPage = 10)
    {
        return $this->courseRepository->searchCourses($query, $perPage);
    }
}