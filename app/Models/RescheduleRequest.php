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
        'status', 
        'approved_by'
    ];

    public function occurrence()
    {
        return $this->belongsTo(LessonOccurrence::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
