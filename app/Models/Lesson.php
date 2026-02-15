<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Instructor;
use App\Models\Student;
use App\Models\LessonOccurrence;

class Lesson extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'subject', 
        'instructor_id', 
        'student_id',
        'start_time', 
        'duration_minutes', 
        'recurrence_type', 
        'recurrence_meta',
    ];

    protected $casts = [
        'recurrence_meta' => 'array',
        'start_time' => 'datetime',
    ];

    /**
     * IMPORTANT: All lessons are stored in Africa/Lagos (UTC+1) timezone in the database
     * This ensures consistent behavior for cron jobs and scheduled activities
     * 
     * When displaying to learners, the framework will convert FROM Africa/Lagos 
     * TO their local timezone via the format-time component or getter
     */
    
    public function getStartTimeAttribute($value)
    {
        // Return as Carbon instance in Africa/Lagos timezone
        // This is how it's stored in the database
        if ($value) {
            return \Carbon\Carbon::parse($value)->setTimezone('Africa/Lagos');
        }
        return null;
    }
    
    public function setStartTimeAttribute($value)
    {
        // Store in Africa/Lagos timezone
        // Ensure the datetime is interpreted as Africa/Lagos
        if ($value) {
            // If it's a string, parse it as Africa/Lagos
            if (is_string($value)) {
                $this->attributes['start_time'] = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $value, 'Africa/Lagos');
            } else {
                // If it's already a Carbon instance, ensure it's in Africa/Lagos
                $this->attributes['start_time'] = $value instanceof \Carbon\Carbon ? $value : \Carbon\Carbon::parse($value);
            }
        }
    }

    // Lesson → Instructor
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class, 'instructor_id');
    }

    // Lesson → Student
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    // Lesson → LessonOccurrences
    public function occurrences(): HasMany
    {
        return $this->hasMany(LessonOccurrence::class);
    }


}
