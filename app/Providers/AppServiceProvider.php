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
