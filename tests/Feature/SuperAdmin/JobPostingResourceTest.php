<?php

use App\Enums\JobPostingStatus;
use App\Enums\UserRole;
use App\Filament\SuperAdmin\Resources\JobPostings\Pages\ManageJobPostings;
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

it('can force close a job posting as super admin', function () {
    $jobPosting = JobPosting::factory()->create([
        'status' => JobPostingStatus::Published,
    ]);

    Livewire::actingAs($this->superAdmin)
        ->test(ManageJobPostings::class)
        ->callTableAction('forceClose', $jobPosting)
        ->assertHasNoTableActionErrors();

    $this->assertDatabaseHas('job_postings', [
        'id' => $jobPosting->id,
        'status' => JobPostingStatus::Closed->value,
    ]);
});

it('can archive a job posting as super admin', function () {
    $jobPosting = JobPosting::factory()->create([
        'status' => JobPostingStatus::Published,
    ]);

    Livewire::actingAs($this->superAdmin)
        ->test(ManageJobPostings::class)
        ->callTableAction('archive', $jobPosting)
        ->assertHasNoTableActionErrors();

    $this->assertDatabaseHas('job_postings', [
        'id' => $jobPosting->id,
        'status' => JobPostingStatus::Archived->value,
    ]);
});