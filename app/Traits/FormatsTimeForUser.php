<?php

namespace App\Traits;

use Carbon\Carbon;

trait FormatsTimeForUser
{
    /**
     * Format a Carbon date for the authenticated user's timezone.
     * Falls back to application timezone if no user is authenticated.
     *
     * @param Carbon $date The date to format
     * @param string $format The format string (default: 'd M Y h:i A')
     * @return string Formatted date string
     */
    public function formatForUser(Carbon $date, $format = 'd M Y h:i A'): string
    {
        $timezone = (auth()->user() && auth()->user()->timezone) ? auth()->user()->timezone : config('app.timezone');
        return $date->setTimezone($timezone)->format($format);
    }

    /**
     * Format a date with just time (h:i A)
     *
     * @param Carbon $date The date to format
     * @return string Formatted time string
     */
    public function formatTimeForUser(Carbon $date): string
    {
        $timezone = (auth()->user() && auth()->user()->timezone) ? auth()->user()->timezone : config('app.timezone');
        return $date->setTimezone($timezone)->format('h:i A');
    }

    /**
     * Format a date with full date and time
     *
     * @param Carbon $date The date to format
     * @return string Formatted date-time string
     */
    public function formatDateTimeForUser(Carbon $date): string
    {
        $timezone = (auth()->user() && auth()->user()->timezone) ? auth()->user()->timezone : config('app.timezone');
        return $date->setTimezone($timezone)->format('d M Y h:i A');
    }

    /**
     * Format a date with just the date (d M Y)
     *
     * @param Carbon $date The date to format
     * @return string Formatted date string
     */
    public function formatDateForUser(Carbon $date): string
    {
        $timezone = (auth()->user() && auth()->user()->timezone) ? auth()->user()->timezone : config('app.timezone');
        return $date->setTimezone($timezone)->format('d M Y');
    }
}
