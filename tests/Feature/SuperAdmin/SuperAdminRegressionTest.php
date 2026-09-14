<?php

use App\Filament\SuperAdmin\Resources\ApplicationResource\Pages\ListApplications;
use App\Filament\SuperAdmin\Resources\JobPostings\Pages\ManageJobPostings;
use App\Filament\SuperAdmin\Resources\PostModerationResource\Pages\ListPosts;
use App\Filament\SuperAdmin\Resources\UserResource\Pages\CreateUser;
use App\Filament\SuperAdmin\Resources\UserResource\Pages\ListUsers;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel('super-admin');
});

test('employer and employee cannot access super admin panel routes', function (string $persona, string $path) {
    $user = User::factory()->{$persona}()->create();

    $this->actingAs($user, 'filament')
        ->get($path)
        ->assertForbidden();
})->with([
    'employer dashboard' => ['employer', '/super-admin'],
    'employee dashboard' => ['employee', '/super-admin'],
    'employer users' => ['employer', '/super-admin/users'],
    'employee users' => ['employee', '/super-admin/users'],
    'employer users create' => ['employer', '/super-admin/users/create'],
    'employee users create' => ['employee', '/super-admin/users/create'],
    'employer job postings' => ['employer', '/super-admin/job-postings'],
    'employee job postings' => ['employee', '/super-admin/job-postings'],
    'employer applications' => ['employer', '/super-admin/applications'],
    'employee applications' => ['employee', '/super-admin/applications'],
    'employer activity logs' => ['employer', '/super-admin/activity-logs'],
    'employee activity logs' => ['employee', '/super-admin/activity-logs'],
    'employer post moderation' => ['employer', '/super-admin/post-moderation'],
    'employee post moderation' => ['employee', '/super-admin/post-moderation'],
]);

test('employer and employee cannot mount super admin livewire resources', function (string $persona, string $pageClass) {
    $user = User::factory()->{$persona}()->create();

    $this->actingAs($user, 'filament');

    Livewire::test($pageClass)->assertForbidden();
})->with([
    'employer users list' => ['employer', ListUsers::class],
    'employee users list' => ['employee', ListUsers::class],
    'employer users create' => ['employer', CreateUser::class],
    'employee users create' => ['employee', CreateUser::class],
    'employer jobs' => ['employer', ManageJobPostings::class],
    'employee jobs' => ['employee', ManageJobPostings::class],
    'employer applications' => ['employer', ListApplications::class],
    'employee applications' => ['employee', ListApplications::class],
    'employer post moderation' => ['employer', ListPosts::class],
    'employee post moderation' => ['employee', ListPosts::class],
]);
