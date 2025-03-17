<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'url',
        'duration',
        'course_id',
        'order',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}