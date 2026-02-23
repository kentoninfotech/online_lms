<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'courses';

    protected $fillable = [
        'code',
        'title',
        'subtitle',
        'description',
        'category_id',
        'facilitator_id',
        'fee',
        'currency',
        'is_free',
        'featured_image',
        'course_hours',
        'is_online',
        'is_offline',
        'is_featured',
        'is_active',
        'max_enrollees',
        'enrolled_count'
    ];

    protected $casts = [
        'fee' => 'decimal:2',
        'is_free' => 'boolean',
        'is_online' => 'boolean',
        'is_offline' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the category this course belongs to
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(CourseCategory::class, 'category_id');
    }

    /**
     * Get the facilitator of this course
     */
    public function facilitator(): BelongsTo
    {
        return $this->belongsTo(Facilitator::class, 'facilitator_id');
    }

    /**
     * Get all facilitators (tutors) for this course
     */
    public function facilitators(): BelongsToMany
    {
        return $this->belongsToMany(Facilitator::class, 'course_facilitator')
            ->withPivot('order', 'bio')
            ->orderBy('course_facilitator.order')
            ->withTimestamps();
    }

    /**
     * Get all dates for this course
     */
    public function courseDates(): HasMany
    {
        return $this->hasMany(CourseDate::class, 'course_id')
            ->orderBy('sequence');
    }

    /**
     * Get all enrollees for this course
     */
    public function enrollees(): HasMany
    {
        return $this->hasMany(CourseEnrollee::class, 'course_id');
    }

    /**
     * Get active enrollments
     */
    public function activeEnrollees(): HasMany
    {
        return $this->hasMany(CourseEnrollee::class, 'course_id')
            ->where('status', 'active');
    }

    /**
     * Get all course contents
     */
    public function contents(): HasMany
    {
        return $this->hasMany(CourseContent::class, 'course_id')
            ->orderBy('sequence');
    }

    /**
     * Get all quizzes
     */
    public function quizzes(): HasMany
    {
        return $this->hasMany(CourseQuiz::class, 'course_id')
            ->orderBy('sequence');
    }

    /**
     * Get all discussions
     */
    public function discussions(): HasMany
    {
        return $this->hasMany(CourseDiscussion::class, 'course_id');
    }

    /**
     * Get all live sessions
     */
    public function liveSessions(): HasMany
    {
        return $this->hasMany(CourseLiveSession::class, 'course_id');
    }

    /**
     * Get all course payments
     */
    public function payments(): HasMany
    {
        return $this->hasMany(CoursePayment::class, 'course_id');
    }

    /**
     * Get all testimonials for this course
     */
    public function testimonials(): HasMany
    {
        return $this->hasMany(CourseTestimonial::class, 'course_id');
    }

    /**
     * Get approved testimonials for this course
     */
    public function approvedTestimonials(): HasMany
    {
        return $this->hasMany(CourseTestimonial::class, 'course_id')
            ->where('is_approved', true)
            ->orderByDesc('submitted_at');
    }

    /**
     * Get bulk messages for this course
     */
    public function bulkMessages(): HasMany
    {
        return $this->hasMany(CourseBulkMessage::class, 'course_id')
            ->orderByDesc('created_at');
    }
}
