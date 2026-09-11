<?php

use App\Enums\JobPostingStatus;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Support\Carbon;

it('persists a published job posting with casts and employer relationship', function () {
    $employer = User::factory()->employer()->create();

    $jobPosting = JobPosting::factory()
        ->published()
        ->create([
            'employer_id' => $employer->id,
        ]);

    expect($jobPosting->status)->toBe(JobPostingStatus::Published)
        ->and($jobPosting->is_active)->toBeTrue()
        ->and($jobPosting->published_at)->toBeInstanceOf(Carbon::class)
        ->and($jobPosting->employer->is($employer))->toBeTrue();

    $this->assertDatabaseHas('job_postings', [
        'id' => $jobPosting->id,
        'employer_id' => $employer->id,
        'status' => JobPostingStatus::Published->value,
        'is_active' => true,
    ]);
});

it('creates a published job posting with the published factory state', function () {
    $jobPosting = JobPosting::factory()
        ->published()
        ->create();

    expect($jobPosting->status)->toBe(JobPostingStatus::Published)
        ->and($jobPosting->published_at)->toBeInstanceOf(Carbon::class);
});

it('creates a draft job posting with the draft factory state', function () {
    $jobPosting = JobPosting::factory()
        ->draft()
        ->create();

    expect($jobPosting->status)->toBe(JobPostingStatus::Draft)
        ->and($jobPosting->published_at)->toBeNull();
});
