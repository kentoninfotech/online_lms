<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function occurrences()
    {
        return $this->hasMany(LessonOccurrence::class);
    }

}
