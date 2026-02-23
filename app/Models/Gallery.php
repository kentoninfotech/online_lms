<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gallery extends Model
{
    protected $fillable = [
        'title',
        'description',
        'event_name',
        'event_date',
        'slug',
        'published',
    ];

    protected $casts = [
        'published' => 'boolean',
        'event_date' => 'datetime',
    ];

    /**
     * Get the images for this gallery.
     */
    public function images(): HasMany
    {
        return $this->hasMany(GalleryImage::class)->orderBy('sequence');
    }

    /**
     * Scope to get published galleries
     */
    public function scopePublished($query)
    {
        return $query->where('published', true);
    }
}
