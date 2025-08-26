<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ZoomSession extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'lesson_occurrence_id', 
        'zoom_meeting_id', 
        'topic', 
        'join_url', 
        'start_url'
    ];

    public function occurrence()
    {
        return $this->belongsTo(LessonOccurrence::class);
    }
}
