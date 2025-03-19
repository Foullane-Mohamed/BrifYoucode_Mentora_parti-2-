<?php

namespace App\Services;

use App\Repositories\Interfaces\CourseRepositoryInterface;
use Illuminate\Support\Str;

class CourseService
{
    protected $courseRepository;

    public function __construct(CourseRepositoryInterface $courseRepository)
    {
        $this->courseRepository = $courseRepository;
    }

    public function getAllCourses()
    {
        return $this->courseRepository->all();
    }

    public function getCourseById($id)
    {
        return $this->courseRepository->find($id);
    }

    public function getCourseWithVideos($id)
    {
        return $this->courseRepository->getWithVideos($id);
    }

    public function getCourseWithEnrollments($id)
    {
        return $this->courseRepository->getWithEnrollments($id);
    }

    public function createCourse(array $data)
    {
        // Generate slug from name
        $data['slug'] = Str::slug($data['name']);
        
        return $this->courseRepository->create($data);
    }

    public function updateCourse($id, array $data)
    {
        // Update slug if name is changed
        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        
        return $this->courseRepository->update($id, $data);
    }

    public function deleteCourse($id)
    {
        return $this->courseRepository->delete($id);
    }

    public function getCoursesByMentor($mentorId)
    {
        return $this->courseRepository->getByMentor($mentorId);
    }

    public function getCoursesByCategory($categoryId)
    {
        return $this->courseRepository->getByCategory($categoryId);
    }

    public function getCoursesBySubcategory($subcategoryId)
    {
        return $this->courseRepository->getBySubcategory($subcategoryId);
    }

    public function getCoursesByTag($tagId)
    {
        return $this->courseRepository->getByTag($tagId);
    }

    public function getCoursesByStatus($status)
    {
        return $this->courseRepository->getByStatus($status);
    }

    public function attachTags($courseId, array $tagIds)
    {
        return $this->courseRepository->attachTags($courseId, $tagIds);
    }

    public function detachTags($courseId, array $tagIds)
    {
        return $this->courseRepository->detachTags($courseId, $tagIds);
    }
}