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
        // Priority 1: Get timezone from request header (sent by JavaScript)
        $detectedTimezone = $request->header('X-User-Timezone');
        
        // If user is authenticated
        if (auth()->check()) {
            $user = auth()->user();
            $userTimezone = $user->timezone ?? config('app.timezone');
            
            // If we detected a timezone from the browser and it's different from stored one, update it
            if ($detectedTimezone && $this->isValidTimezone($detectedTimezone) && $detectedTimezone !== $userTimezone) {
                $user->update(['timezone' => $detectedTimezone]);
                \Log::debug('User timezone auto-updated', [
                    'user_id' => $user->id,
                    'old_timezone' => $userTimezone,
                    'new_timezone' => $detectedTimezone,
                ]);
                $userTimezone = $detectedTimezone;
            }
            
            session(['user_timezone' => $userTimezone]);
        } else {
            // For guests, use detected timezone or default
            $timezone = $detectedTimezone ?? config('app.timezone');
            
            if ($timezone && $this->isValidTimezone($timezone)) {
                session(['user_timezone' => $timezone]);
            } else {
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
