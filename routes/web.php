<?php

use App\Enums\UserRole;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\NetworkController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PeopleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileEducationController;
use App\Http\Controllers\ProfileExperienceController;
use App\Http\Controllers\ProfileSkillController;
use App\Http\Controllers\SavedItemController;
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

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    Route::get('/saved', [SavedItemController::class, 'index'])->name('saved.index');
    Route::post('/saved/jobs/{job}', [SavedItemController::class, 'storeJob'])->name('saved.jobs.store');
    Route::delete('/saved/jobs/{job}', [SavedItemController::class, 'destroyJob'])->name('saved.jobs.destroy');
    Route::post('/saved/posts/{post}', [SavedItemController::class, 'storePost'])->name('saved.posts.store');
    Route::delete('/saved/posts/{post}', [SavedItemController::class, 'destroyPost'])->name('saved.posts.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/professional', [ProfileController::class, 'updateProfessional'])->name('profile.professional.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/profile/experiences', [ProfileExperienceController::class, 'store'])->name('profile.experiences.store');
    Route::patch('/profile/experiences/{experience}', [ProfileExperienceController::class, 'update'])->name('profile.experiences.update');
    Route::delete('/profile/experiences/{experience}', [ProfileExperienceController::class, 'destroy'])->name('profile.experiences.destroy');

    Route::post('/profile/educations', [ProfileEducationController::class, 'store'])->name('profile.educations.store');
    Route::patch('/profile/educations/{education}', [ProfileEducationController::class, 'update'])->name('profile.educations.update');
    Route::delete('/profile/educations/{education}', [ProfileEducationController::class, 'destroy'])->name('profile.educations.destroy');

    Route::post('/profile/skills', [ProfileSkillController::class, 'store'])->name('profile.skills.store');
    Route::patch('/profile/skills/{skill}', [ProfileSkillController::class, 'update'])->name('profile.skills.update');
    Route::delete('/profile/skills/{skill}', [ProfileSkillController::class, 'destroy'])->name('profile.skills.destroy');
});

require __DIR__.'/auth.php';
