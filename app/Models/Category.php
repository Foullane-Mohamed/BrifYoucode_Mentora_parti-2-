<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }

    public function courses()
    {
        return $this->hasManyThrough(Course::class, Subcategory::class);
    }
}