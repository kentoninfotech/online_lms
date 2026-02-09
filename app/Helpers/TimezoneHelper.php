<?php

if (!function_exists('getUserTimezone')) {
    /**
     * Get the user's timezone from session or authenticated user
     */
    function getUserTimezone(): string
    {
        // Try session first
        if (session('user_timezone')) {
            return session('user_timezone');
        }

        // Try authenticated user attribute if it exists
        if (auth()->check() && auth()->user() && property_exists(auth()->user(), 'timezone') && auth()->user()->timezone) {
            return auth()->user()->timezone;
        }

        // Default to config
        return config('app.timezone');
    }
}

if (!function_exists('toUserTimezone')) {
    /**
     * Convert a datetime to user's timezone
     */
    function toUserTimezone($datetime, $format = 'd M Y h:i A'): string
    {
        if (!$datetime) {
            return 'Not Available';
        }

        try {
            $tz = getUserTimezone();
            return \Carbon\Carbon::parse($datetime)
                ->setTimezone($tz)
                ->format($format);
        } catch (\Exception) {
            return 'Invalid Date';
        }
    }
}

if (!function_exists('toUtcTimezone')) {
    /**
     * Convert from user's timezone to UTC for storage
     */
    function toUtcTimezone($datetime, $userTimezone = null): \Carbon\Carbon
    {
        if (!$userTimezone) {
            $userTimezone = getUserTimezone();
        }

        try {
            return \Carbon\Carbon::createFromFormat('Y-m-d H:i', $datetime, $userTimezone)->setTimezone('UTC');
        } catch (\Exception) {
            return \Carbon\Carbon::parse($datetime)->setTimezone('UTC');
        }
    }
}
