<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class LessonOccurrence extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'lesson_id', 
        'scheduled_start', 
        'duration_minutes', 
        'status',
    ];

    protected $casts = [
        'scheduled_start' => 'datetime',
    ];

    // LessonOccurrence → Lesson
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    // Zoom Session
    public function zoomSession(): HasOne
    {
        return $this->hasOne(ZoomSession::class);
    }

    // Attendance Records
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    // Reschedule Requests
    public function rescheduleRequests(): HasMany
    {
        return $this->hasMany(RescheduleRequest::class);
    }

    // Accessor for scheduled_end
    public function getScheduledEndAttribute()
    {
        return $this->scheduled_start
            ? Carbon::parse($this->scheduled_start)->copy()->addMinutes((int) $this->duration_minutes)
            : null;
    }

    // Ongoing 
    public function scopeOngoing($query)
    {
        $now = now();
        return $query->where('scheduled_start', '<=', $now)
                    ->whereRaw('DATE_ADD(scheduled_start, INTERVAL duration_minutes MINUTE) >= ?', [$now]);
    }
}
