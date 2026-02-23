<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'course_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'icon',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all courses in this category
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'category_id');
    }

    /**
     * Get active courses in this category
     */
    public function activeCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'category_id')
            ->where('is_active', true);
    }
}
