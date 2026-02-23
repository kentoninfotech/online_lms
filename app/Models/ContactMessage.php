<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactMessage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'message',
        'phone',
        'subject',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the responses for this contact message
     */
    public function responses()
    {
        return $this->hasMany(ContactResponse::class);
    }

    /**
     * Get the latest admin response
     */
    public function latestResponse()
    {
        return $this->hasOne(ContactResponse::class)->latestOfMany();
    }

    /**
     * Scope: get unread messages
     */
    public function scopeUnread($query)
    {
        return $query->where('status', 'unread');
    }

    /**
     * Scope: get replied messages
     */
    public function scopeReplied($query)
    {
        return $query->where('status', 'replied');
    }

    /**
     * Mark as read
     */
    public function markAsRead()
    {
        $this->update(['status' => 'read']);
    }

    /**
     * Mark as replied
     */
    public function markAsReplied()
    {
        $this->update(['status' => 'replied']);
    }
}
