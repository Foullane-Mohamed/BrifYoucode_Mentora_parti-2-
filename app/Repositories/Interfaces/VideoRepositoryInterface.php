<?php

namespace App\Repositories\Interfaces;

interface VideoRepositoryInterface extends BaseRepositoryInterface
{
    public function getByCourse($courseId);
}