<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class CourseLesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'instructor_id',
        'topic',
        'description',
        'scheduled_date',
        'start_time',
        'end_time',
        'location',
        'meeting_link',
        'is_published',
        'capacity',
        'notes',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_published' => 'boolean',
    ];

    protected $appends = ['formatted_date_time', 'status', 'duration_minutes'];

    // CourseLesson → Course
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    // CourseLesson → Instructor
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class);
    }

    // CourseLesson → Attendees (CourseEnrollees)
    public function attendees(): HasMany
    {
        return $this->hasMany(CourseLessonAttendee::class);
    }

    /**
     * Get formatted date and time for display
     */
    public function getFormattedDateTimeAttribute(): string
    {
        if ($this->start_time && $this->end_time) {
            return $this->start_time->format('M d, Y') . ' | ' . 
                   $this->start_time->format('h:i A') . ' - ' . 
                   $this->end_time->format('h:i A');
        }
        return 'Not scheduled';
    }

    /**
     * Get lesson status (upcoming, ongoing, completed, cancelled)
     */
    public function getStatusAttribute(): string
    {
        if (!$this->start_time) {
            return 'draft';
        }

        $now = now();
        
        if ($now < $this->start_time) {
            return 'upcoming';
        } elseif ($now >= $this->start_time && $now <= $this->end_time) {
            return 'ongoing';
        } else {
            return 'completed';
        }
    }

    /**
     * Get duration in minutes
     */
    public function getDurationMinutesAttribute(): ?int
    {
        if ($this->start_time && $this->end_time) {
            return $this->end_time->diffInMinutes($this->start_time);
        }
        return null;
    }

    /**
     * Scopes
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>', now())->orderBy('start_time', 'asc');
    }

    public function scopeForCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeForInstructor($query, $instructorId)
    {
        return $query->where('instructor_id', $instructorId);
    }

    public function scopeInMonth($query, $month, $year)
    {
        return $query->whereMonth('start_time', $month)
                    ->whereYear('start_time', $year);
    }
}
