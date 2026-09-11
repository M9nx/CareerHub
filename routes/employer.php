<?php

use App\Http\Controllers\Employer\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'employer'])
    ->prefix('employer')
    ->group(function () {
        Route::get('/dashboard', DashboardController::class)
            ->name('employer.dashboard');
    });