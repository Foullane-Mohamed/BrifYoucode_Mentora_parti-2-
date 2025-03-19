<?php

namespace App\Repositories;

use App\Models\Tag;
use App\Models\Course;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\TagRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TagRepository extends BaseRepository implements TagRepositoryInterface
{
    /**
     * TagRepository constructor.
     * 
     * @param Tag $model
     */
    public function __construct(Tag $model)
    {
        parent::__construct($model);
    }

    /**
     * @inheritDoc
     */
    public function getTagBySlug(string $slug): ?Tag
    {
        return $this->model->where('slug', $slug)->first();
    }

    /**
     * @inheritDoc
     */
    public function getTagWithCourses(int $tagId): ?Tag
    {
        return $this->model->with(['courses' => function ($query) {
            $query->where('is_published', true);
        }])->findOrFail($tagId);
    }

    /**
     * @inheritDoc
     */
    public function getPopularTags(int $limit = 10): Collection
    {
        return $this->model->withCount('courses')
            ->orderBy('courses_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function syncWithCourse(int $courseId, array $tagIds): bool
    {
        $course = Course::findOrFail($courseId);
        $course->tags()->sync($tagIds);
        return true;
    }
}