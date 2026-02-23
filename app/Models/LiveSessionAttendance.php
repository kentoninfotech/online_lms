<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveSessionAttendance extends Model
{
    use HasFactory;

    protected $table = 'live_session_attendances';

    protected $fillable = [
        'live_session_id',
        'user_id',
        'joined_at',
        'left_at',
        'duration_minutes',
        'attendance_status'
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    /**
     * Get the session
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(CourseLiveSession::class, 'live_session_id');
    }

    /**
     * Get the attendee
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
