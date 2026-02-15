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
        // Always use the configured app timezone (Africa/Lagos for Nigeria)
        // Ignore browser timezone detection to ensure consistency
        $defaultTimezone = config('app.timezone');
        
        session(['user_timezone' => $defaultTimezone]);

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
        } catch (\Exception $e) {
            return false;
        }
    }
}
