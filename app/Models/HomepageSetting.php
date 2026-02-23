<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HomepageSetting extends Model
{
    use HasFactory;

    protected $table = 'homepage_settings';

    protected $fillable = [
        'section',
        'key',
        'value',
        'image_path',
        'button_text',
        'button_link',
        'title',
        'description',
        'data_type',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Get all active settings for a section
     */
    public static function getSection($section)
    {
        return self::where('section', $section)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->keyBy('key');
    }

    /**
     * Get a specific setting value
     */
    public static function getSetting($section, $key, $default = null)
    {
        $setting = self::where('section', $section)
            ->where('key', $key)
            ->where('is_active', true)
            ->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Get a specific image path setting
     */
    public static function getImagePath($section, $key, $default = null)
    {
        $setting = self::where('section', $section)
            ->where('key', $key)
            ->where('is_active', true)
            ->first();

        return $setting && $setting->image_path ? asset($setting->image_path) : $default;
    }

    /**
     * bulk get all homepage settings organized by section and keyed by key
     */
    public static function getAllSections()
    {
        $settings = self::where('is_active', true)
            ->orderBy('section')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('section');
        
        // Also key each section's items by their 'key' field for easier access
        return $settings->map(function($section) {
            return $section->keyBy('key');
        });
    }

    /**
     * Update or create a setting
     */
    public static function setSetting($section, $key, $value, $dataType = 'text')
    {
        return self::updateOrCreate(
            ['section' => $section, 'key' => $key],
            [
                'value' => $value,
                'data_type' => $dataType,
                'is_active' => true
            ]
        );
    }
}
