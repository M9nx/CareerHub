<?php

use App\Enums\ApplicationStatus;
use App\Enums\JobPostingStatus;
use App\Filament\SuperAdmin\Widgets\StatsOverviewWidget;
use App\Models\Application;
use App\Models\JobPosting;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel('super-admin');
});

test('super admin stats overview widget renders correct counts', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $employer = User::factory()->employer()->create();
    $employees = User::factory()->employee()->count(3)->create();

    $publishedJobs = JobPosting::factory()
        ->count(2)
        ->for($employer, 'employer')
        ->published()
        ->create();

    JobPosting::factory()
        ->for($employer, 'employer')
        ->create(['status' => JobPostingStatus::Draft]);

    foreach ($employees as $employee) {
        Application::factory()
            ->for($employee, 'employee')
            ->for($publishedJobs->first(), 'jobPosting')
            ->create(['status' => ApplicationStatus::Submitted]);
    }

    Application::factory()
        ->for($employees->first(), 'employee')
        ->for($publishedJobs->last(), 'jobPosting')
        ->create(['status' => ApplicationStatus::Accepted]);

    $this->actingAs($superAdmin, 'filament');

    // superAdmin + employer + 3 employees
    Livewire::test(StatsOverviewWidget::class)
        ->assertOk()
        ->assertSee(__('Total Users'))
        ->assertSee('5')
        ->assertSee(__('Open Jobs'))
        ->assertSee('2')
        ->assertSee(__('Pending Applications'))
        ->assertSee('3');
});

test('employer cannot access the stats overview widget', function () {
    $employer = User::factory()->employer()->create();

    $this->actingAs($employer, 'filament');

    expect(StatsOverviewWidget::canView())->toBeFalse();

    Livewire::test(StatsOverviewWidget::class)
        ->assertForbidden();
});
