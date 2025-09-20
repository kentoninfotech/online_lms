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
        // Define scheduled tasks
        $schedule->command('lessons:generate-occurrences')->daily();
        $schedule->command('lessons:create-zoom-sessions')->daily();
        $schedule->command('zoom:sync-participants')->dailyAt('02:00');
        $schedule->command('reminders:classes')->everyFiveMinutes();
        $schedule->job(new SendSubscriptionExpiryWarnings)->daily();
        $schedule->job(new SendBillingOverdueReminders)->daily();
    })
    
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();


    