<?php

namespace App\Repositories\Interfaces;

interface CourseRepositoryInterface extends BaseRepositoryInterface
{
    public function getByMentor($mentorId);
    public function getWithVideos($id);
    public function getWithEnrollments($id);
    public function getByStatus($status);
    public function getByCategory($categoryId);
    public function getBySubcategory($subcategoryId);
    public function getByTag($tagId);
    public function attachTags($courseId, array $tagIds);
    public function detachTags($courseId, array $tagIds);
}