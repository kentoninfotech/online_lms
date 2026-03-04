<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Instructor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name', 
        'email',
        'bio', 
        'number', 
        'address',
        'zoom_link',
        'specialization',
    ];

    // Relationships, Accessors, Mutators, etc. can be added here
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class, 'instructor_id');
    }

    /**
     * Get all courses assigned to this instructor
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'instructor_course')
            ->withPivot('role', 'bio', 'order', 'can_manage_content', 'can_manage_enrollees', 'can_manage_quizzes', 'is_active')
            ->orderBy('instructor_course.order')
            ->withTimestamps();
    }

    /**
     * Get only active courses assigned to this instructor
     */
    public function activeCourses(): BelongsToMany
    {
        return $this->courses()
            ->wherePivot('is_active', true);
    }
}
