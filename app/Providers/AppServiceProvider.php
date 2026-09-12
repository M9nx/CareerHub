<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use App\Models\Application;
use App\Policies\ApplicationPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        // SuperAdmin bypass: short-circuits every ability check.
        // Returning null (not false) for non-SuperAdmins lets normal
        // policy resolution continue instead of denying outright.
        Gate::before(function (User $user, string $ability) {
            return $user->isSuperAdmin() ? true : null;
        });

        // Explicit policy registration (in addition to Laravel's
        // auto-discovery convention, so this is unambiguous in code review).
        Gate::policy(User::class, UserPolicy::class);
    }
}
        Gate::policy(Application::class, ApplicationPolicy::class);
    }
}