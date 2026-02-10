<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Notifications\ChannelManager;
use App\Notifications\Channels\SmsChannel;
use App\Services\SmsService;
use Illuminate\Pagination\Paginator;
use App\Models\Lesson;
use App\Observers\LessonObserver;
use App\Models\LessonOccurrence;
use App\Observers\LessonOccurrenceObserver;

class AppServiceProvider extends ServiceProvider
{


    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Bootstrap pagination style
        Paginator::useBootstrapFive();

        // Register the custom SMS channel with Laravel's ChannelManager
        $this->app->afterResolving(ChannelManager::class, function (ChannelManager $manager) {
            $manager->extend('sms', function ($app) {
                return new SmsChannel($app->make(SmsService::class));
            });
        });

        Lesson::observe(LessonObserver::class);
        LessonOccurrence::observe(LessonOccurrenceObserver::class);

        // Define timezone helper functions
        if (!function_exists('getUserTimezone')) {
            function getUserTimezone(): string
            {
                if (session('user_timezone')) {
                    return session('user_timezone');
                }
                return config('app.timezone');
            }
        }

        if (!function_exists('toUserTimezone')) {
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
    }
}
