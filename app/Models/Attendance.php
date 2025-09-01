<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attendance extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'lesson_occurrence_id',
        'attendable_id',
        'attendable_type',
        'join_time',
        'leave_time',
        'duration_minutes',
        'status',
        'zoom_user_id',
        'raw',
    ];

    protected $casts = [
        'join_time'  => 'datetime',
        'leave_time' => 'datetime',
        'raw'        => 'array',
    ];

    // Attendance → LessonOccurrence
    public function occurrence(): BelongsTo
    {
        return $this->belongsTo(LessonOccurrence::class, 'lesson_occurrence_id');
    }

    // Attendance → Student or Instructor
    public function attendable(): MorphTo
    {
        return $this->morphTo();
    }
    
}
