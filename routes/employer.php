<?php

use App\Http\Controllers\Employer\DashboardController;
use App\Http\Controllers\Employer\JobPostingController;
use App\Http\Controllers\Employer\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'employer'])
    ->prefix('employer')
    ->group(function () {
        Route::get('/dashboard', DashboardController::class)
            ->name('employer.dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])
            ->name('employer.profile.edit');

        Route::patch('/profile', [ProfileController::class, 'update'])
            ->name('employer.profile.update');

        Route::resource('jobs', JobPostingController::class)
            ->except(['show'])
            ->names('employer.jobs');

        Route::post('jobs/{job}/publish', [JobPostingController::class, 'publish'])
            ->name('employer.jobs.publish');

        Route::post('jobs/{job}/close', [JobPostingController::class, 'close'])
            ->name('employer.jobs.close');
    });
