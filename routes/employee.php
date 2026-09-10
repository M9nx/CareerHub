<?php

use App\Http\Controllers\Employee\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'employee'])
    ->prefix('employee')
    ->group(function () {
        Route::get('/dashboard', DashboardController::class)
            ->name('employee.dashboard');
    });