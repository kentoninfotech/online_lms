<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facilitator extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'facilitators';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'bio',
        'profile_image',
        'qualification',
        'expertise',
        'is_verified',
        'is_active'
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user associated with this facilitator
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all courses facilitated by this user (via many-to-many)
     */
    public function assignedCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_facilitator')
            ->withPivot('order', 'bio')
            ->orderBy('course_facilitator.order')
            ->withTimestamps();
    }

    /**
     * Get all courses facilitated by this user (legacy - single facilitator)
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'facilitator_id');
    }

    /**
     * Get all live sessions conducted by this facilitator
     */
    public function liveSessions(): HasMany
    {
        return $this->hasMany(CourseLiveSession::class, 'facilitator_id');
    }
}
