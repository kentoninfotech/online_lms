<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseVenue extends Model
{
    use HasFactory;

    protected $table = 'course_venues';

    protected $fillable = [
        'course_date_id',
        'sequence',
        'venue_name',
        'address',
        'city',
        'state',
        'country',
        'latitude',
        'longitude',
        'capacity',
        'fee',
        'enrolled_count',
        'notes'
    ];

    protected $casts = [
        'latitude' => 'double',
        'longitude' => 'double',
        'fee' => 'decimal:2',
    ];

    /**
     * Get the course date this venue belongs to
     */
    public function courseDate(): BelongsTo
    {
        return $this->belongsTo(CourseDate::class, 'course_date_id');
    }

    /**
     * Get all enrollees for this venue
     */
    public function enrollees(): HasMany
    {
        return $this->hasMany(CourseEnrollee::class, 'course_venue_id');
    }

    /**
     * Check if venue is at capacity
     */
    public function isAtCapacity(): bool
    {
        if ($this->capacity === null) {
            return false;
        }
        return $this->enrolled_count >= $this->capacity;
    }
}
