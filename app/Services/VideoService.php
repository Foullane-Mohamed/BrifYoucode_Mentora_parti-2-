<?php

namespace App\Services;

use App\Repositories\Interfaces\VideoRepositoryInterface;

class VideoService
{
    protected $videoRepository;

    public function __construct(VideoRepositoryInterface $videoRepository)
    {
        $this->videoRepository = $videoRepository;
    }

    public function getAllVideos()
    {
        return $this->videoRepository->all();
    }

    public function getVideoById($id)
    {
        return $this->videoRepository->find($id);
    }

    public function getVideosByCourse($courseId)
    {
        return $this->videoRepository->getByCourse($courseId);
    }

    public function createVideo(array $data)
    {
        return $this->videoRepository->create($data);
    }

    public function updateVideo($id, array $data)
    {
        return $this->videoRepository->update($id, $data);
    }

    public function deleteVideo($id)
    {
        return $this->videoRepository->delete($id);
    }
}