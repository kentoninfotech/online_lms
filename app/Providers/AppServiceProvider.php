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

    }
}
