<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\Lesson;
use App\Observers\LessonObserver;
use App\Models\LessonOccurrence;
use App\Observers\LessonOccurrenceObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     */
    protected $policies = [
        \App\Models\User::class              => \App\Policies\UserPolicy::class,
        \App\Models\Student::class           => \App\Policies\StudentPolicy::class,
        \App\Models\Lesson::class            => \App\Policies\LessonPolicy::class,
        \App\Models\LessonOccurrence::class  => \App\Policies\LessonOccurrencePolicy::class,
        \App\Models\Subscription::class      => \App\Policies\SubscriptionPolicy::class,
        \App\Models\Payment::class           => \App\Policies\PaymentPolicy::class,
        \App\Models\RescheduleRequest::class => \App\Policies\ReschedulePolicy::class,
    ];


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

        Lesson::observe(LessonObserver::class);
        LessonOccurrence::observe(LessonOccurrenceObserver::class);

    }
}
