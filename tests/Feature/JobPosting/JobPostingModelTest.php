<?php

use App\Enums\JobPostingStatus;
use App\Models\JobPosting;
use App\Models\User;

it('persists a job posting with status cast', function () {
    $employer = User::factory()->create();

    $jobPosting = JobPosting::factory()->create([
        'employer_id' => $employer->id,
        'status' => JobPostingStatus::Published,
    ]);

    expect($jobPosting->status)->toBe(JobPostingStatus::Published);

    $this->assertDatabaseHas('job_postings', [
        'id' => $jobPosting->id,
        'employer_id' => $employer->id,
        'status' => JobPostingStatus::Published->value,
    ]);
});
