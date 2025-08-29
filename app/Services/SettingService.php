<?php

namespace App\Services;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    protected $cacheKey = 'settings.all';

    /**
     * Get a setting value by key with caching.
     */
    public function get($key, $default = null)
    {
        $settings = Cache::rememberForever($this->cacheKey, function () {
            return Setting::pluck('value', 'key')->toArray();
        });

        return $settings[$key] ?? $default;
    }
    /**
     * Set or update a setting value and clear the cache.
     */
    public function set($key, $value)
    {
        Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget($this->cacheKey);
    }
    /**
     * Get all settings as key-value pairs with caching.
     */
    public function all()
    {
        return Cache::rememberForever($this->cacheKey, function () {
            return Setting::pluck('value', 'key')->toArray();
        });
    }
}
