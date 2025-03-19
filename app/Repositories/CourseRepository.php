<?php

namespace App\Repositories;

use App\Models\Course;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CourseRepository extends BaseRepository implements CourseRepositoryInterface
{
    /**
     * CourseRepository constructor.
     * 
     * @param Course $model
     */
    public function __construct(Course $model)
    {
        parent::__construct($model);
    }

    /**
     * @inheritDoc
     */
    public function getPublishedCourses(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->where('is_published', true)
            ->with(['user', 'subcategory.category', 'tags'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * @inheritDoc
     */
    public function getCoursesByMentor(int $mentorId): Collection
    {
        return $this->model->where('user_id', $mentorId)
            ->with(['subcategory.category', 'tags'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function getCourseBySlug(string $slug): ?Course
    {
        return $this->model->where('slug', $slug)
            ->with(['user', 'subcategory.category', 'tags'])
            ->first();
    }

    /**
     * @inheritDoc
     */
    public function getCourseWithVideos(int $courseId): ?Course
    {
        return $this->model->with(['videos' => function ($query) {
            $query->orderBy('order', 'asc');
        }])->findOrFail($courseId);
    }

    /**
     * @inheritDoc
     */
    public function getCourseWithTags(int $courseId): ?Course
    {
        return $this->model->with('tags')->findOrFail($courseId);
    }

    /**
     * @inheritDoc
     */
    public function getCourseWithEnrollments(int $courseId): ?Course
    {
        return $this->model->with(['enrollments.user'])->findOrFail($courseId);
    }

    /**
     * @inheritDoc
     */
    public function publishCourse(int $courseId): bool
    {
        $course = $this->findById($courseId);
        return $course->update(['is_published' => true]);
    }

    /**
     * @inheritDoc
     */
    public function unpublishCourse(int $courseId): bool
    {
        $course = $this->findById($courseId);
        return $course->update(['is_published' => false]);
    }

    /**
     * @inheritDoc
     */
    public function searchCourses(string $query, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->where('is_published', true)
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->with(['user', 'subcategory.category', 'tags'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}