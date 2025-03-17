<?php

namespace App\Repositories\Interfaces;

interface EnrollmentRepositoryInterface extends BaseRepositoryInterface
{
    public function getByCourse($courseId);
    public function getByStudent($studentId);
    public function getByStatus($status);
    public function updateStatus($id, $status);
    public function updateProgress($id, $progress);
}