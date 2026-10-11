<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseLessonAttendee extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_lesson_id',
        'course_enrollee_id',
        'attended_at',
        'status',
        'feedback',
    ];

    protected $casts = [
        'attended_at' => 'datetime',
    ];

    // CourseLessonAttendee → CourseLesson
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(CourseLesson::class, 'course_lesson_id');
    }

    // CourseLessonAttendee → CourseEnrollee
    public function enrollee(): BelongsTo
    {
        return $this->belongsTo(CourseEnrollee::class, 'course_enrollee_id');
    }
}
