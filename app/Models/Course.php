<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'duration',
        'difficulty',
        'subcategory_id',
        'user_id',
        'status',
    ];

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function category()
    {
        return $this->subcategory->category();
    }

    public function mentor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments')
            ->withPivot('status', 'progress', 'completed_at')
            ->withTimestamps();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}