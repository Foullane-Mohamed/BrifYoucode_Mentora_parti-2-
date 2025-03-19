<?php

namespace App\Repositories\Interfaces;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;

interface TagRepositoryInterface extends EloquentRepositoryInterface
{
    /**
     * Get tag by slug.
     * 
     * @param string $slug
     * @return Tag|null
     */
    public function getTagBySlug(string $slug): ?Tag;

    /**
     * Get tag with courses.
     * 
     * @param int $tagId
     * @return Tag|null
     */
    public function getTagWithCourses(int $tagId): ?Tag;

    /**
     * Get popular tags.
     * 
     * @param int $limit
     * @return Collection
     */
    public function getPopularTags(int $limit = 10): Collection;

    /**
     * Sync tags with course.
     * 
     * @param int $courseId
     * @param array $tagIds
     * @return bool
     */
    public function syncWithCourse(int $courseId, array $tagIds): bool;
}