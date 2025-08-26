<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'lesson_occurrence_id', 
        'user_id', 
        'join_time', 
        'leave_time', 
        'duration_minutes', 
        'status', 
        'zoom_user_id'
    ];

    public function occurrence()
    {
        return $this->belongsTo(LessonOccurrence::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
