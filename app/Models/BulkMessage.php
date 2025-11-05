<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BulkMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject', 
        'message', 
        'methods', 
        'status', 
        'sender'
    ];

    protected $casts = [
        'methods' => 'array'
    ];

    public function recipients(): HasMany
    {
        return $this->hasMany(BulkMessageRecipient::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender');
    }
}
