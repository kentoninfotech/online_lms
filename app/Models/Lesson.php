<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    // Lesson → Instructor
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    // Lesson → Student
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // Lesson → LessonOccurrences
    public function occurrences(): HasMany
    {
        return $this->hasMany(LessonOccurrence::class);
    }

}
