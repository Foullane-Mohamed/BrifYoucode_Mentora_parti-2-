<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'subcategory_id',
        'title',
        'slug',
        'description',
        'requirements',
        'objectives',
        'thumbnail',
        'level',
        'is_published',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_published' => 'boolean',
    ];

    /**
     * Get the user (mentor) that owns the course.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subcategory that owns the course.
     */
    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    /**
     * Get the category for the course through the subcategory.
     */
    public function category()
    {
        return $this->hasOneThrough(
            Category::class,
            Subcategory::class,
            'id', // Foreign key on subcategories table
            'id', // Foreign key on categories table
            'subcategory_id', // Local key on courses table
            'category_id' // Local key on subcategories table
        );
    }

    /**
     * Get the videos for the course.
     */
    public function videos()
    {
        return $this->hasMany(Video::class)->orderBy('order');
    }

    /**
     * Get the tags for the course.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Get the enrollments for the course.
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the students enrolled in the course.
     */
    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments')
            ->withPivot('status', 'progress', 'completed_at')
            ->withTimestamps();
    }
}