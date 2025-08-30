<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RescheduleRequest extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'lesson_occurrence_id', 
        'requested_by', 
        'proposed_start', 
        'reason',
        'status', 
        'approved_by'
    ];

    protected $casts = [
        'new_start_time' => 'datetime',
        'new_end_time'   => 'datetime',
    ];

    // RescheduleRequest → LessonOccurrence
    public function occurrence()
    {
        return $this->belongsTo(LessonOccurrence::class);
    }

    // RescheduleRequest → Requester (User)
    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    // RescheduleRequest → Approver (User)
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
