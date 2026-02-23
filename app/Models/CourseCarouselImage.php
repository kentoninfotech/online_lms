<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseCarouselImage extends Model
{
    use HasFactory;

    protected $table = 'course_carousel_images';

    protected $fillable = [
        'image_path',
        'title',
        'description',
        'button_text',
        'button_link',
        'sort_order',
        'is_active',
        'display_from',
        'display_until'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_from' => 'datetime',
        'display_until' => 'datetime',
    ];

    /**
     * Get active carousel images
     */
    public static function active()
    {
        return self::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('display_from')
                  ->orWhere('display_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('display_until')
                  ->orWhere('display_until', '>=', now());
            })
            ->orderBy('sort_order');
    }
}
