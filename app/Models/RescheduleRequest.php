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
        'approved_by',
        'decision_reason'
    ];

    protected $casts = [
        'proposed_start' => 'datetime',
    ];

    // RescheduleRequest → LessonOccurrence
    public function occurrence()
    {
        return $this->belongsTo(LessonOccurrence::class, 'lesson_occurrence_id');
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
