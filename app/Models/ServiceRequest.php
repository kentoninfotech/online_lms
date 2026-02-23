<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequest extends Model
{
    protected $fillable = [
        'service_id',
        'name',
        'email',
        'phone',
        'message',
        'status',
    ];

    /**
     * Get the service this request belongs to.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Scope to get pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Mark request as contacted
     */
    public function markContacted()
    {
        $this->update(['status' => 'contacted']);
    }

    /**
     * Mark request as completed
     */
    public function markCompleted()
    {
        $this->update(['status' => 'completed']);
    }
}
