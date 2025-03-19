<?php

namespace App\Services;

use App\Models\Tag;
use App\Repositories\Interfaces\TagRepositoryInterface;
use Illuminate\Support\Str;

class TagService
{
    /**
     * @var TagRepositoryInterface
     */
    protected $tagRepository;

    /**
     * TagService constructor.
     * 
     * @param TagRepositoryInterface $tagRepository
     */
    public function __construct(TagRepositoryInterface $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * Get all tags.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllTags()
    {
        return $this->tagRepository->all();
    }

    /**
     * Get tag by ID.
     * 
     * @param int $id
     * @return Tag
     */
    public function getTagById($id)
    {
        return $this->tagRepository->findById($id);
    }

    /**
     * Get tag by slug.
     * 
     * @param string $slug
     * @return Tag|null
     */
    public function getTagBySlug($slug)
    {
        return $this->tagRepository->getTagBySlug($slug);
    }

    /**
     * Get tag with courses.
     * 
     * @param int $tagId
     * @return Tag|null
     */
    public function getTagWithCourses($tagId)
    {
        return $this->tagRepository->getTagWithCourses($tagId);
    }

    /**
     * Get popular tags.
     * 
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPopularTags($limit = 10)
    {
        return $this->tagRepository->getPopularTags($limit);
    }

    /**
     * Create new tag.
     * 
     * @param array $data
     * @return Tag
     */
    public function createTag(array $data)
    {
        // Generate slug
        $data['slug'] = Str::slug($data['name']);
        
        return $this->tagRepository->create($data);
    }

    /**
     * Update tag.
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateTag($id, array $data)
    {
        // Generate slug if name is provided
        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        
        return $this->tagRepository->update($id, $data);
    }

    /**
     * Delete tag.
     * 
     * @param int $id
     * @return bool
     */
    public function deleteTag($id)
    {
        return $this->tagRepository->deleteById($id);
    }

    /**
     * Sync tags with course.
     * 
     * @param int $courseId
     * @param array $tagIds
     * @return bool
     */
    public function syncTagsWithCourse($courseId, array $tagIds)
    {
        return $this->tagRepository->syncWithCourse($courseId, $tagIds);
    }
}