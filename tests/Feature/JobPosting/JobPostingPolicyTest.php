<?php

namespace Tests\Feature\JobPosting;

use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobPostingPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_employer_cannot_update_another_employers_job_posting(): void
    {
        $employer = User::factory()->employer()->create();
        $otherEmployer = User::factory()->employer()->create();

        $jobPosting = JobPosting::factory()->create([
            'employer_id' => $otherEmployer->id,
        ]);

        $this->assertFalse(
            $employer->can('update', $jobPosting)
        );
    }
}
