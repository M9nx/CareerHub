<?php

use App\Enums\JobPostingStatus;
use App\Filament\SuperAdmin\Resources\JobPostings\Pages\ManageJobPostings;
use App\Filament\SuperAdmin\Resources\JobPostings\Pages\ViewJobPosting;
use App\Models\JobPosting;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel('super-admin');
});

test('super admin can force close a job posting', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $job = JobPosting::factory()->published()->create();

    $this->actingAs($superAdmin, 'filament');

    Livewire::test(ManageJobPostings::class)
        ->callAction(TestAction::make('forceClose')->table($job))
        ->assertNotified();

    expect($job->fresh()->status)->toBe(JobPostingStatus::Closed);
});

test('super admin can archive a job posting', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $job = JobPosting::factory()->published()->create();

    $this->actingAs($superAdmin, 'filament');

    Livewire::test(ManageJobPostings::class)
        ->callAction(TestAction::make('archive')->table($job))
        ->assertNotified();

    expect($job->fresh()->status)->toBe(JobPostingStatus::Archived);
});

test('super admin can view a job posting', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $employer = User::factory()->employer()->create([
        'name' => 'Ada Employer',
    ]);
    $job = JobPosting::factory()->for($employer, 'employer')->published()->create([
        'title' => 'Published Career Role',
        'description' => 'Build and maintain Laravel applications.',
    ]);

    $this->actingAs($superAdmin, 'filament');

    Livewire::test(ViewJobPosting::class, ['record' => $job->getRouteKey()])
        ->assertSuccessful()
        ->assertSee('Published Career Role')
        ->assertSee('Ada Employer')
        ->assertSee('Build and maintain Laravel applications.');
});

test('force close is hidden when the job posting is already closed', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $closed = JobPosting::factory()->closed()->create();
    $published = JobPosting::factory()->published()->create();

    $this->actingAs($superAdmin, 'filament');

    Livewire::test(ManageJobPostings::class)
        ->assertActionHidden(TestAction::make('forceClose')->table($closed))
        ->assertActionVisible(TestAction::make('forceClose')->table($published));
});

test('archive is hidden when the job posting is already archived', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $archived = JobPosting::factory()->archived()->create();
    $published = JobPosting::factory()->published()->create();

    $this->actingAs($superAdmin, 'filament');

    Livewire::test(ManageJobPostings::class)
        ->assertActionHidden(TestAction::make('archive')->table($archived))
        ->assertActionVisible(TestAction::make('archive')->table($published));
});

test('employer cannot access job posting management', function () {
    $employer = User::factory()->employer()->create();

    $this->actingAs($employer, 'filament')
        ->get('/super-admin/job-postings')
        ->assertForbidden();

    Livewire::test(ManageJobPostings::class)
        ->assertForbidden();
});

test('job posting titles are escaped on the list', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    JobPosting::factory()->published()->create([
        'title' => '<script>alert("xss")</script>',
    ]);

    $this->actingAs($superAdmin, 'filament');

    Livewire::test(ManageJobPostings::class)
        ->assertSuccessful()
        ->assertDontSee('<script>alert("xss")</script>', false)
        ->assertSee('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', false);
});
