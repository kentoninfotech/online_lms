<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteVisitor extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'country',
        'location',
        'visited_url',
        'visited_at',
        'user_agent',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
