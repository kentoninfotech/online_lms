<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LessonOccurrence extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'lesson_id', 
        'scheduled_start', 
        'duration_minutes', 
        'status',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function zoomSession()
    {
        return $this->hasOne(ZoomSession::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function rescheduleRequests()
    {
        return $this->hasMany(RescheduleRequest::class);
    }
}
