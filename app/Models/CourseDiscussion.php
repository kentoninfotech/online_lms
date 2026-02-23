<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseDiscussion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'course_discussions';

    protected $fillable = [
        'course_id',
        'user_id',
        'title',
        'message',
        'is_pinned',
        'is_locked',
        'replies_count'
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
    ];

    /**
     * Get the course
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * Get the author
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user (alias for author)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all replies
     */
    public function replies(): HasMany
    {
        return $this->hasMany(DiscussionReply::class, 'discussion_id');
    }
}
