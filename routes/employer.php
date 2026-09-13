<?php

use App\Http\Controllers\Employer\DashboardController;
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
    });
