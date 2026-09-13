<?php

use App\Http\Controllers\Employee\DashboardController;
use App\Http\Controllers\Employee\JobBrowseController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'employee'])
    ->prefix('employee')
    ->group(function () {
        Route::get('/dashboard', DashboardController::class)
            ->name('employee.dashboard');

        Route::get('/jobs', [JobBrowseController::class, 'index'])
            ->name('employee.jobs.index');

        Route::get('/jobs/{jobPosting}', [JobBrowseController::class, 'show'])
            ->name('employee.jobs.show');
    });