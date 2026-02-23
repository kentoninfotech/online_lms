<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseTestimonial extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'course_testimonials';

    protected $fillable = [
        'course_id',
        'user_id',
        'enrollee_id',
        'rating',
        'title',
        'content',
        'is_approved',
        'is_featured',
        'submitted_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
        'is_featured' => 'boolean',
        'submitted_at' => 'datetime',
    ];

    /**
     * Get the course this testimonial belongs to
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the user who gave the testimonial
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course enrollee (student enrollment details)
     */
    public function enrollee(): BelongsTo
    {
        return $this->belongsTo(CourseEnrollee::class);
    }

    /**
     * Get approved testimonials only
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Get featured testimonials only
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Get recent testimonials
     */
    public function scopeRecent($query)
    {
        return $query->orderByDesc('submitted_at');
    }
}
