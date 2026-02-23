<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'body',
        'featured_image',
        'slug',
        'published',
    ];

    protected $casts = [
        'published' => 'boolean',
    ];

    /**
     * Get the service requests for this service.
     */
    public function requests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class);
    }

    /**
     * Scope to get published services
     */
    public function scopePublished($query)
    {
        return $query->where('published', true);
    }
}
