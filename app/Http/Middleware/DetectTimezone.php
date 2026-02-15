<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetectTimezone
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Priority 1: If user is authenticated, use their stored timezone
        if (auth()->check() && auth()->user()->timezone) {
            session(['user_timezone' => auth()->user()->timezone]);
        } else {
            // Priority 2: Get timezone from request header (sent by JavaScript)
            $timezone = $request->header('X-User-Timezone') ?? $request->cookie('user_timezone');
            
            if ($timezone && $this->isValidTimezone($timezone)) {
                session(['user_timezone' => $timezone]);
            } else {
                // Priority 3: Default to config timezone
                if (!session('user_timezone')) {
                    session(['user_timezone' => config('app.timezone')]);
                }
            }
        }

        return $next($request);
    }

    /**
     * Check if timezone is valid
     */
    private function isValidTimezone($timezone): bool
    {
        try {
            new \DateTimeZone($timezone);
            return true;
        } catch (\Exception) {
            return false;
        }
    }
}
