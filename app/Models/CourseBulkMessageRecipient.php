<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseBulkMessageRecipient extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'course_bulk_message_recipients';

    protected $fillable = [
        'course_bulk_message_id',
        'user_id',
        'email',
        'phone',
        'status',
        'response',
        'sent_at'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    /**
     * Get the message
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(CourseBulkMessage::class, 'course_bulk_message_id');
    }

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
