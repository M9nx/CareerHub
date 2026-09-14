<?php

use App\Enums\ApplicationStatus;
use App\Enums\JobPostingStatus;
use App\Enums\UserRole;
use App\Filament\SuperAdmin\Widgets\StatsOverviewWidget;
use App\Models\Application;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);
beforeEach(function () {
    $this->superAdmin = User::factory()->create([
        'role' => UserRole::SuperAdmin,
    ]);
});
it('renders super admin stats overview widget with correct counts', function () {
    User::factory()->count(3)->create();
    JobPosting::factory()->count(2)->create(['status' => JobPostingStatus::Published]);
    Application::factory()->count(4)->create(['status' => ApplicationStatus::Pending]);
    $this->actingAs($this->superAdmin);
    Livewire::test(StatsOverviewWidget::class)
        ->assertSee('Total Users')
        ->assertSee('Open Jobs')
        ->assertSee('Pending Applications');
});
