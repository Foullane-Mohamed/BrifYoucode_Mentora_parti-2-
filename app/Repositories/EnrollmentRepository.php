<?php

namespace App\Repositories;

use App\Models\Enrollment;
use App\Repositories\BaseRepository;
use App\Repositories\Interfaces\EnrollmentRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class EnrollmentRepository extends BaseRepository implements EnrollmentRepositoryInterface
{
    /**
     * EnrollmentRepository constructor.
     * 
     * @param Enrollment $model
     */
    public function __construct(Enrollment $model)
    {
        parent::__construct($model);
    }

    /**
     * @inheritDoc
     */
    public function getEnrollmentsByUser(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)
            ->with(['course.user', 'course.subcategory.category'])
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function getEnrollmentsByCourse(int $courseId): Collection
    {
        return $this->model->where('course_id', $courseId)
            ->with('user')
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function getEnrollmentsByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)
            ->with(['user', 'course.user'])
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function getEnrollmentByUserAndCourse(int $userId, int $courseId): ?Enrollment
    {
        return $this->model->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();
    }

    /**
     * @inheritDoc
     */
    public function updateEnrollmentStatus(int $enrollmentId, string $status): bool
    {
        $enrollment = $this->findById($enrollmentId);
        return $enrollment->update(['status' => $status]);
    }

    /**
     * @inheritDoc
     */
    public function updateEnrollmentProgress(int $enrollmentId, float $progress): bool
    {
        $enrollment = $this->findById($enrollmentId);
        
        $data = ['progress' => $progress];
        
        // If progress is 100%, mark as completed
        if ($progress >= 100) {
            $data['completed_at'] = Carbon::now();
        }
        
        return $enrollment->update($data);
    }

    /**
     * @inheritDoc
     */
    public function completeEnrollment(int $enrollmentId): bool
    {
        $enrollment = $this->findById($enrollmentId);
        
        return $enrollment->update([
            'progress' => 100,
            'completed_at' => Carbon::now()
        ]);
    }
}