<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseDate extends Model
{
    use HasFactory;

    protected $table = 'course_dates';

    protected $fillable = [
        'course_id',
        'start_date',
        'end_date',
        'date_label',
        'sequence',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the course this date belongs to
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * Get all venues for this course date
     */
    public function venues(): HasMany
    {
        return $this->hasMany(CourseVenue::class, 'course_date_id');
    }

    /**
     * Get all enrollees for this date
     */
    public function enrollees(): HasMany
    {
        return $this->hasMany(CourseEnrollee::class, 'course_date_id');
    }
}
