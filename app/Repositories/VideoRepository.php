<?php

namespace App\Repositories;

use App\Models\Video;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\VideoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class VideoRepository extends BaseRepository implements VideoRepositoryInterface
{
    /**
     * VideoRepository constructor.
     * 
     * @param Video $model
     */
    public function __construct(Video $model)
    {
        parent::__construct($model);
    }

    /**
     * @inheritDoc
     */
    public function getVideosByCourse(int $courseId): Collection
    {
        return $this->model->where('course_id', $courseId)
            ->orderBy('order', 'asc')
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function updateVideoOrder(int $videoId, int $order): bool
    {
        $video = $this->findById($videoId);
        return $video->update(['order' => $order]);
    }

    /**
     * @inheritDoc
     */
    public function reorderVideos(int $courseId, array $videoOrders): bool
    {
        try {
            DB::beginTransaction();
            
            foreach ($videoOrders as $videoId => $order) {
                $video = $this->findById($videoId);
                
                if ($video->course_id != $courseId) {
                    DB::rollBack();
                    return false;
                }
                
                $video->update(['order' => $order]);
            }
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
}