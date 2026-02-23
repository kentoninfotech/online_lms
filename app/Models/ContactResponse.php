<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactResponse extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'contact_message_id',
        'admin_id',
        'response_text',
        'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the contact message this response belongs to
     */
    public function message()
    {
        return $this->belongsTo(ContactMessage::class, 'contact_message_id');
    }

    /**
     * Get the admin who responded
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
