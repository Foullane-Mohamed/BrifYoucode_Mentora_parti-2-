<?php

namespace App\Repositories\Interfaces;

use App\Models\Video;
use Illuminate\Database\Eloquent\Collection;

interface VideoRepositoryInterface extends EloquentRepositoryInterface
{
    /**
     * Get videos by course.
     * 
     * @param int $courseId
     * @return Collection
     */
    public function getVideosByCourse(int $courseId): Collection;

    /**
     * Update video order.
     * 
     * @param int $videoId
     * @param int $order
     * @return bool
     */
    public function updateVideoOrder(int $videoId, int $order): bool;

    /**
     * Reorder videos.
     * 
     * @param int $courseId
     * @param array $videoOrders
     * @return bool
     */
    public function reorderVideos(int $courseId, array $videoOrders): bool;
}