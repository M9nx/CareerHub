<?php

use App\Enums\UserRole;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();

        return match ($user->role) {
            UserRole::Employer => redirect()->route('employer.dashboard'),
            UserRole::Employee => redirect()->route('employee.dashboard'),
            UserRole::SuperAdmin => redirect('/super-admin'),
            default => redirect()->route('dashboard'),
        };
    }

    return view('landing');
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->role === UserRole::Employer) {
        return redirect()->route('employer.dashboard');
    }

    if ($user->role === UserRole::Employee) {
        return redirect()->route('employee.dashboard');
    }

    if ($user->role === UserRole::SuperAdmin) {
        return redirect('/super-admin');
    }

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
