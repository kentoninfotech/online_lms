<?php

if (!function_exists('getUserTimezone')) {
    /**
     * Get the user's timezone from session or authenticated user
     * Defaults to Nigeria's timezone (Africa/Lagos)
     */
    function getUserTimezone(): string
    {
        // Always default to Africa/Lagos (Nigeria timezone) for consistency
        // All lesson times should be displayed in Nigerian time
        return 'Africa/Lagos';
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
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
            return \Carbon\Carbon::parse($datetime)->setTimezone('UTC');
        }
    }
}
