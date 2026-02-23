<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseLiveSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'course_live_sessions';

    protected $fillable = [
        'course_id',
        'facilitator_id',
        'title',
        'description',
        'scheduled_start',
        'scheduled_end',
        'session_type',
        'meeting_link',
        'meeting_id',
        'meeting_password',
        'status',
        'attendees_count',
        'actual_start',
        'actual_end',
        'recording_url',
        'is_compulsory',
        'duration_minutes',
        'max_points',
        'jitsi_room_name',
        'chat_enabled'
    ];

    protected $casts = [
        'scheduled_start' => 'datetime',
        'scheduled_end' => 'datetime',
        'actual_start' => 'datetime',
        'actual_end' => 'datetime',
        'is_compulsory' => 'boolean',
        'chat_enabled' => 'boolean',
        'max_points' => 'integer',
        'duration_minutes' => 'integer',
    ];

    /**
     * Get the course
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * Get the facilitator
     */
    public function facilitator(): BelongsTo
    {
        return $this->belongsTo(Facilitator::class, 'facilitator_id');
    }

    /**
     * Get all attendances
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(LiveSessionAttendance::class, 'live_session_id');
    }

    /**
     * Check if session is happening now
     */
    public function isLive(): bool
    {
        return $this->status === 'live' || 
               (now()->isBetween($this->scheduled_start, $this->scheduled_end) && $this->status !== 'completed');
    }
}
