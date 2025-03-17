<?php

namespace App\Repositories;

use App\Models\Course;
use App\Repositories\Interfaces\CourseRepositoryInterface;

class CourseRepository extends BaseRepository implements CourseRepositoryInterface
{
    public function __construct(Course $model)
    {
        parent::__construct($model);
    }

    public function getByMentor($mentorId)
    {
        return $this->model->where('user_id', $mentorId)->get();
    }

    public function getWithVideos($id)
    {
        return $this->model->with('videos')->findOrFail($id);
    }

    public function getWithEnrollments($id)
    {
        return $this->model->with('enrollments')->findOrFail($id);
    }

    public function getByStatus($status)
    {
        return $this->model->where('status', $status)->get();
    }

    public function getByCategory($categoryId)
    {
        return $this->model->whereHas('subcategory', function ($query) use ($categoryId) {
            $query->where('category_id', $categoryId);
        })->get();
    }

    public function getBySubcategory($subcategoryId)
    {
        return $this->model->where('subcategory_id', $subcategoryId)->get();
    }

    public function getByTag($tagId)
    {
        return $this->model->whereHas('tags', function ($query) use ($tagId) {
            $query->where('tags.id', $tagId);
        })->get();
    }

    public function attachTags($courseId, array $tagIds)
    {
        $course = $this->find($courseId);
        $course->tags()->attach($tagIds);
        return $course;
    }

    public function detachTags($courseId, array $tagIds)
    {
        $course = $this->find($courseId);
        $course->tags()->detach($tagIds);
        return $course;
    }
}