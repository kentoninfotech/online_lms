<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseContentCompletion extends Model
{
    use HasFactory;

    protected $table = 'course_content_completions';

    protected $fillable = [
        'course_enrollee_id',
        'course_content_id',
        'time_spent_minutes',
        'is_completed',
        'completed_at',
        'started_at',
        'progress_percentage'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'started_at' => 'datetime',
        'progress_percentage' => 'decimal:2',
    ];

    /**
     * Get the enrollment
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(CourseEnrollee::class, 'course_enrollee_id');
    }

    /**
     * Get the content
     */
    public function content(): BelongsTo
    {
        return $this->belongsTo(CourseContent::class, 'course_content_id');
    }

    /**
     * Mark content as started
     */
    public function markStarted(): self
    {
        if (!$this->started_at) {
            $this->update(['started_at' => now()]);
        }
        return $this;
    }

    /**
     * Mark content as completed
     */
    public function markCompleted(): self
    {
        $this->update([
            'is_completed' => true,
            'completed_at' => now(),
            'progress_percentage' => 100
        ]);
        return $this;
    }
}
