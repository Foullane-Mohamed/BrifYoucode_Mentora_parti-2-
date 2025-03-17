<?php

namespace App\Repositories;

use App\Models\Enrollment;
use App\Repositories\Interfaces\EnrollmentRepositoryInterface;

class EnrollmentRepository extends BaseRepository implements EnrollmentRepositoryInterface
{
    public function __construct(Enrollment $model)
    {
        parent::__construct($model);
    }

    public function getByCourse($courseId)
    {
        return $this->model->where('course_id', $courseId)->get();
    }

    public function getByStudent($studentId)
    {
        return $this->model->where('user_id', $studentId)->get();
    }

    public function getByStatus($status)
    {
        return $this->model->where('status', $status)->get();
    }

    public function updateStatus($id, $status)
    {
        $enrollment = $this->find($id);
        $enrollment->status = $status;
        $enrollment->save();
        return $enrollment;
    }

    public function updateProgress($id, $progress)
    {
        $enrollment = $this->find($id);
        $enrollment->progress = $progress;
        if ($progress >= 100) {
            $enrollment->completed_at = now();
        }
        $enrollment->save();
        return $enrollment;
    }
}