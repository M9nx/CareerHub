<?php

namespace App\Providers;

use App\Models\Application;
use App\Policies\ApplicationPolicy;
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
        Gate::policy(Application::class, ApplicationPolicy::class);
    }
}