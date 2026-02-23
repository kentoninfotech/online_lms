<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseBulkMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'course_bulk_messages';

    protected $fillable = [
        'course_id',
        'sender_id',
        'subject',
        'message',
        'methods',
        'status',
        'scheduled_at',
        'total_recipients',
        'sent_count',
        'notes'
    ];

    protected $casts = [
        'methods' => 'array',
        'scheduled_at' => 'datetime',
    ];

    /**
     * Get the course
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * Get the sender (admin/tutor)
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get all recipients
     */
    public function recipients(): HasMany
    {
        return $this->hasMany(CourseBulkMessageRecipient::class, 'course_bulk_message_id');
    }
}
