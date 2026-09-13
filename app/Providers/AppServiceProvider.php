<?php

namespace App\Providers;

use App\Models\Application;
use App\Models\JobPosting;
use App\Models\User;
use App\Observers\JobPostingObserver;
use App\Policies\ApplicationPolicy;
use App\Policies\UserPolicy;
use App\Services\LocalDocumentStorage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        app()->singleton(LocalDocumentStorage::class, function () {
            return new LocalDocumentStorage;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function (User $user, string $ability): ?bool {
            return $user->isSuperAdmin() ? true : null;
        });

        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Application::class, ApplicationPolicy::class);

        JobPosting::observe(JobPostingObserver::class);
    }
}
