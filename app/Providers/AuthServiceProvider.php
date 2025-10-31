<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
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
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Optionally define any Gate::define statements here
    }
}