<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class TimezoneServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load timezone helpers
        require_once app_path('Helpers/TimezoneHelper.php');
    }
}
