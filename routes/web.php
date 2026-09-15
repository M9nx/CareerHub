<?php

use App\Enums\UserRole;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\NetworkController;
use App\Http\Controllers\PeopleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();

        return match ($user->role) {
            UserRole::Employer, UserRole::Employee => redirect()->route('feed.index'),
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
    Route::get('/feed', [FeedController::class, 'index'])->name('feed.index');
    Route::post('/feed', [FeedController::class, 'store'])->name('feed.store');
    Route::post('/feed/posts/{post}/react', [FeedController::class, 'react'])->name('feed.posts.react');
    Route::post('/feed/posts/{post}/share', [FeedController::class, 'share'])->name('feed.posts.share');
    Route::post('/feed/posts/{post}/comments', [FeedController::class, 'storeComment'])->name('feed.posts.comments.store');
    Route::delete('/feed/posts/{post}/comments/{comment}', [FeedController::class, 'destroyComment'])->name('feed.posts.comments.destroy');

    Route::get('/search', SearchController::class)->name('search.index');

    Route::get('/people/{user}', [PeopleController::class, 'show'])->name('people.show');

    Route::get('/network', [NetworkController::class, 'index'])->name('network.index');
    Route::post('/network/people/{user}/connect', [ConnectionController::class, 'store'])->name('network.connect');
    Route::post('/network/connections/{connection}/accept', [ConnectionController::class, 'accept'])->name('network.connections.accept');
    Route::post('/network/connections/{connection}/reject', [ConnectionController::class, 'reject'])->name('network.connections.reject');
    Route::post('/network/connections/{connection}/withdraw', [ConnectionController::class, 'withdraw'])->name('network.connections.withdraw');
    Route::delete('/network/connections/{connection}', [ConnectionController::class, 'destroy'])->name('network.connections.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/professional', [ProfileController::class, 'updateProfessional'])->name('profile.professional.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
