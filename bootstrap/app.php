<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use App\Jobs\SendSubscriptionExpiryWarnings;
use App\Jobs\SendBillingOverdueReminders;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register route middleware aliases
        // $middleware->alias([
        //     'isAdmin' => App\Http\Middleware\IsAdmin::class,
        // ]);
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
        // Send payment reminders daily
        $schedule->job(new SendSubscriptionExpiryWarnings)->daily();
        // Send billing overdue reminders daily
        $schedule->job(new SendBillingOverdueReminders)->daily();

        // $schedule->command('lessons:create-zoom-sessions')->daily();
        // $schedule->command('zoom:sync-participants')->dailyAt('02:00');
    })
    
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();


    