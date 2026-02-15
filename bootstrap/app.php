<?php

// Early fallback timezone helper functions — define these as a last-resort
// fallback so that views/middleware/controllers can safely call them
// even if Composer autoload or deployment hasn't been run.
if (!function_exists('getUserTimezone')) {
    function getUserTimezone(): string {
        try {
            return session('user_timezone', config('app.timezone'));
        } catch (\Throwable $e) {
            return config('app.timezone');
        }
    }
}

if (!function_exists('toUserTimezone')) {
    function toUserTimezone($datetime, $format = 'd M Y h:i A'): string {
        try {
            $tz = getUserTimezone();
            $c = \Carbon\Carbon::parse($datetime)->setTimezone($tz);
            return $c->format($format);
        } catch (\Throwable $e) {
            return is_string($datetime) ? $datetime : (string)$datetime;
        }
    }
}

if (!function_exists('toUtcTimezone')) {
    function toUtcTimezone($datetime, $userTimezone = null): \Carbon\Carbon {
        $tz = $userTimezone ?: (function_exists('getUserTimezone') ? getUserTimezone() : config('app.timezone'));
        try {
            return \Carbon\Carbon::parse($datetime, $tz)->setTimezone('UTC');
        } catch (\Throwable $e) {
            return \Carbon\Carbon::parse($datetime)->setTimezone('UTC');
        }
    }
}

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use App\Jobs\SendSubscriptionExpiryWarnings;
use App\Jobs\SendBillingOverdueReminders;

return Application::configure(dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Add timezone detection middleware
        $middleware->append(\App\Http\Middleware\DetectTimezone::class);
        
        // Register route middleware aliases
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })

    ->withSchedule(function (Schedule $schedule): void {
        // Generate lesson occurrences daily
        $schedule->command('lessons:generate-occurrences')->daily();
        // Update lesson statuses every 1 minute
        $schedule->command('lessons:update-status')->everyFiveMinutes();
        // Finalize attendance every 5 minutes
        $schedule->command('attendance:finalize')->everyFiveMinutes();
        // Send class reminders every 5 minutes
        $schedule->command('reminders:classes')->everyTenMinutes();
        // Update subscription statuses daily
        $schedule->command('subscriptions:update-status')->dailyAt('01:00');
        // Reset reschedule usage daily at 1am
        $schedule->command('reschedule:reset-usage')->dailyAt('01:00');
        // Send payment reminders daily
        $schedule->job(new SendSubscriptionExpiryWarnings)->daily();
        // Send billing overdue reminders daily
        $schedule->job(new SendBillingOverdueReminders)->daily();

        // $schedule->command('lessons:create-zoom-sessions')->daily();
        // $schedule->command('zoom:sync-participants')->dailyAt('02:00');
    })
    
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle TokenMismatchException (419 Page Expired)
        $exceptions->render(function (\Throwable $e, $request) {
            // Handle TokenMismatchException (CSRF token mismatch / session expired)
            if ($e instanceof \Illuminate\Session\TokenMismatchException) {
                // Logout the user
                \Illuminate\Support\Facades\Auth::logout();
                
                // Invalidate the session
                $request->session()->invalidate();
                
                // Regenerate token for next session
                $request->session()->regenerateToken();
                
                // Redirect to login page
                return redirect()->route('login')->with('error', 'Your session has expired. Please log in again.');
            }
        });
    })->create();


    