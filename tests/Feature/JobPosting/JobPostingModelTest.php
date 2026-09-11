<?php

use App\Enums\JobPostingStatus;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Support\Carbon;

it('persists a published job posting with casts and employer relationship', function () {
    $employer = User::factory()->create();

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
