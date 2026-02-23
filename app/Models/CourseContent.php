<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseContent extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'course_contents';

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'content_type',
        'content',
        'file_path',
        'duration_minutes',
        'sequence',
        'section_id',
        'is_published',
        'is_required',
        'available_from',
        'available_until',
        'prerequisite_content_id',
        'min_reading_time_minutes',
        'embed_type',
        'allow_download',
        'track_viewing'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_required' => 'boolean',
        'allow_download' => 'boolean',
        'track_viewing' => 'boolean',
        'available_from' => 'datetime',
        'available_until' => 'datetime',
    ];

    /**
     * Get the course this content belongs to
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * Get all completions for this content
     */
    public function completions(): HasMany
    {
        return $this->hasMany(CourseContentCompletion::class, 'course_content_id');
    }

    /**
     * Get completion count
     */
    public function getCompletionCount(): int
    {
        return $this->completions()->where('is_completed', true)->count();
    }
}
