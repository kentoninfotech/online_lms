<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BulkMessageRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'bulk_message_id', 
        'user_id', 
        'email', 
        'number',
        'delivery_method', 
        'delivery_status', 
        'response_log'
    ];

    public function bulkMessage(): BelongsTo
    {
        return $this->belongsTo(BulkMessage::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
