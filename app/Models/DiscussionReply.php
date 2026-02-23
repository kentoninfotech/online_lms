<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiscussionReply extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'discussion_replies';

    protected $fillable = [
        'discussion_id',
        'user_id',
        'message',
        'reply_to_id'
    ];

    /**
     * Get the discussion
     */
    public function discussion(): BelongsTo
    {
        return $this->belongsTo(CourseDiscussion::class, 'discussion_id');
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
     * Get nested replies
     */
    public function replies(): HasMany
    {
        return $this->hasMany(DiscussionReply::class, 'reply_to_id');
    }

    /**
     * Get parent reply if this is a nested reply
     */
    public function parentReply(): BelongsTo
    {
        return $this->belongsTo(DiscussionReply::class, 'reply_to_id');
    }
}
