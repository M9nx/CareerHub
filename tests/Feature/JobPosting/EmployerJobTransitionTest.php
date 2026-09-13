<?php

namespace Tests\Feature\JobPosting;

use App\Enums\JobPostingStatus;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployerJobTransitionTest extends TestCase
{
    use RefreshDatabase;

    public function test_employer_can_publish_their_own_job_posting(): void
    {
        $employer = User::factory()->employer()->create([
            'is_active' => true,
            'is_blocked_from_posts' => false,
        ]);

        $job = JobPosting::factory()->draft()->create([
            'employer_id' => $employer->id,
            'published_at' => null,
        ]);

        $response = $this
            ->actingAs($employer)
            ->post(route('employer.jobs.publish', $job));

        $response
            ->assertRedirect(route('employer.jobs.edit', $job))
            ->assertSessionHas('success');

        $job->refresh();

        $this->assertSame(JobPostingStatus::Published, $job->status);
        $this->assertNotNull($job->published_at);
    }

    public function test_employer_can_close_their_own_job_posting(): void
    {
        $employer = User::factory()->employer()->create([
            'is_active' => true,
            'is_blocked_from_posts' => false,
        ]);

        $job = JobPosting::factory()->published()->create([
            'employer_id' => $employer->id,
            'published_at' => now(),
        ]);

        $response = $this
            ->actingAs($employer)
            ->post(route('employer.jobs.close', $job));

        $response
            ->assertRedirect(route('employer.jobs.edit', $job))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('job_postings', [
            'id' => $job->id,
            'status' => JobPostingStatus::Closed->value,
        ]);
    }

    public function test_employer_cannot_publish_another_employers_job_posting(): void
    {
        $employer = User::factory()->employer()->create([
            'is_active' => true,
            'is_blocked_from_posts' => false,
        ]);

        $otherEmployer = User::factory()->employer()->create([
            'is_active' => true,
            'is_blocked_from_posts' => false,
        ]);

        $job = JobPosting::factory()->draft()->create([
            'employer_id' => $otherEmployer->id,
        ]);

        $response = $this
            ->actingAs($employer)
            ->post(route('employer.jobs.publish', $job));

        $response->assertForbidden();

        $this->assertDatabaseHas('job_postings', [
            'id' => $job->id,
            'status' => JobPostingStatus::Draft->value,
        ]);
    }

    public function test_employer_cannot_close_another_employers_job_posting(): void
    {
        $employer = User::factory()->employer()->create([
            'is_active' => true,
            'is_blocked_from_posts' => false,
        ]);

        $otherEmployer = User::factory()->employer()->create([
            'is_active' => true,
            'is_blocked_from_posts' => false,
        ]);

        $job = JobPosting::factory()->published()->create([
            'employer_id' => $otherEmployer->id,
        ]);

        $response = $this
            ->actingAs($employer)
            ->post(route('employer.jobs.close', $job));

        $response->assertForbidden();

        $this->assertDatabaseHas('job_postings', [
            'id' => $job->id,
            'status' => JobPostingStatus::Published->value,
        ]);
    }
}