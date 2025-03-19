<?php

namespace App\Services;

use App\Models\Video;
use App\Repositories\Interfaces\VideoRepositoryInterface;
use App\Repositories\Interfaces\CourseRepositoryInterface;

class VideoService
{
    /**
     * @var VideoRepositoryInterface
     */
    protected $videoRepository;

    /**
     * @var CourseRepositoryInterface
     */
    protected $courseRepository;

    /**
     * VideoService constructor.
     * 
     * @param VideoRepositoryInterface $videoRepository
     * @param CourseRepositoryInterface $courseRepository
     */
    public function __construct(
        VideoRepositoryInterface $videoRepository,
        CourseRepositoryInterface $courseRepository
    ) {
        $this->videoRepository = $videoRepository;
        $this->courseRepository = $courseRepository;
    }

    /**
     * Get all videos.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllVideos()
    {
        return $this->videoRepository->all(['*'], ['course']);
    }

    /**
     * Get videos by course.
     * 
     * @param int $courseId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVideosByCourse($courseId)
    {
        return $this->videoRepository->getVideosByCourse($courseId);
    }

    /**
     * Get video by ID.
     * 
     * @param int $id
     * @return Video
     */
    public function getVideoById($id)
    {
        return $this->videoRepository->findById($id, ['*'], ['course']);
    }

    /**
     * Create new video.
     * 
     * @param array $data
     * @return Video
     */
    public function createVideo(array $data)
    {
        // Check if course exists
        $this->courseRepository->findById($data['course_id']);
        
        // Get the highest order value for the course
        $videos = $this->videoRepository->getVideosByCourse($data['course_id']);
        $maxOrder = $videos->max('order') ?? 0;
        
        // Set the order to be the next in sequence
        $data['order'] = $maxOrder + 1;
        
        return $this->videoRepository->create($data);
    }

    /**
     * Update video.
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateVideo($id, array $data)
    {
        // Check if course exists if course_id is provided
        if (isset($data['course_id'])) {
            $this->courseRepository->findById($data['course_id']);
        }
        
        return $this->videoRepository->update($id, $data);
    }

    /**
     * Delete video.
     * 
     * @param int $id
     * @return bool
     */
    public function deleteVideo($id)
    {
        return $this->videoRepository->deleteById($id);
    }

    /**
     * Update video order.
     * 
     * @param int $videoId
     * @param int $order
     * @return bool
     */
    public function updateVideoOrder($videoId, $order)
    {
        return $this->videoRepository->updateVideoOrder($videoId, $order);
    }

    /**
     * Reorder videos.
     * 
     * @param int $courseId
     * @param array $videoOrders
     * @return bool
     */
    public function reorderVideos($courseId, array $videoOrders)
    {
        // Check if course exists
        $this->courseRepository->findById($courseId);
        
        return $this->videoRepository->reorderVideos($courseId, $videoOrders);
    }
}