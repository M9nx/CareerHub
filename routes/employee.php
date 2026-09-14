<?php

use App\Http\Controllers\Employee\ApplicationController;
use App\Http\Controllers\Employee\DashboardController;
use App\Http\Controllers\Employee\JobBrowseController;
use App\Http\Controllers\Employee\PostController;
use App\Http\Controllers\Employee\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'employee'])
    ->prefix('employee')
    ->group(function () {
        Route::get('/dashboard', DashboardController::class)
            ->name('employee.dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])
            ->name('employee.profile.edit');

        Route::patch('/profile', [ProfileController::class, 'update'])
            ->name('employee.profile.update');

        Route::get('/jobs', [JobBrowseController::class, 'index'])
            ->name('employee.jobs.index');

        Route::get('/jobs/{jobPosting}', [JobBrowseController::class, 'show'])
            ->name('employee.jobs.show');

        Route::get('/applications', [ApplicationController::class, 'index'])
            ->name('employee.applications.index');

        Route::post('/applications', [ApplicationController::class, 'store'])
            ->name('employee.applications.store');

        Route::patch(
            '/applications/{application}/cancel',
            [ApplicationController::class, 'cancel']
        )->name('employee.applications.cancel');

        Route::resource('posts', PostController::class)
            ->except(['show'])
            ->names('employee.posts');
    });
