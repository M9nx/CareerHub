<?php

namespace Tests\Feature\JobPosting;

use App\Enums\JobPostingStatus;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployerJobPostingTest extends TestCase
{
    use RefreshDatabase;

    public function test_employer_can_view_their_job_postings(): void
    {
        $employer = User::factory()->employer()->create();

        $ownJob = JobPosting::factory()->create([
            'employer_id' => $employer->id,
        ]);

        JobPosting::factory()->create();

        $response = $this
            ->actingAs($employer)
            ->get(route('employer.jobs.index'));

        $response->assertOk();
        $response->assertSee($ownJob->title);
    }

    public function test_employer_can_view_create_job_form(): void
    {
        $employer = User::factory()->employer()->create([
            'is_active' => true,
            'is_blocked_from_posts' => false,
        ]);

        $response = $this
            ->actingAs($employer)
            ->get(route('employer.jobs.create'));

        $response->assertOk();
        $response->assertSee('Create Job Posting');
    }

    public function test_employer_can_create_a_job_posting(): void
    {
        $employer = User::factory()->employer()->create([
            'is_active' => true,
            'is_blocked_from_posts' => false,
        ]);

        $response = $this
            ->actingAs($employer)
            ->post(route('employer.jobs.store'), [
                'title' => 'Laravel Developer',
                'description' => 'Build and maintain Laravel applications.',
                'status' => JobPostingStatus::Draft->value,
            ]);

        $response
            ->assertRedirect(route('employer.jobs.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('job_postings', [
            'employer_id' => $employer->id,
            'title' => 'Laravel Developer',
            'description' => 'Build and maintain Laravel applications.',
            'status' => JobPostingStatus::Draft->value,
        ]);
    }

    public function test_employer_can_edit_their_own_job_posting(): void
    {
        $employer = User::factory()->employer()->create([
            'is_active' => true,
            'is_blocked_from_posts' => false,
        ]);

        $job = JobPosting::factory()->create([
            'employer_id' => $employer->id,
        ]);

        $response = $this
            ->actingAs($employer)
            ->get(route('employer.jobs.edit', $job));

        $response->assertOk();
        $response->assertSee($job->title);
    }

    public function test_employer_can_update_their_own_job_posting(): void
    {
        $employer = User::factory()->employer()->create([
            'is_active' => true,
            'is_blocked_from_posts' => false,
        ]);

        $job = JobPosting::factory()->create([
            'employer_id' => $employer->id,
            'status' => JobPostingStatus::Draft,
        ]);

        $response = $this
            ->actingAs($employer)
            ->patch(route('employer.jobs.update', $job), [
                'title' => 'Updated Laravel Developer',
                'description' => 'Updated job description.',
                'status' => JobPostingStatus::Published->value,
            ]);

        $response
            ->assertRedirect(route('employer.jobs.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('job_postings', [
            'id' => $job->id,
            'employer_id' => $employer->id,
            'title' => 'Updated Laravel Developer',
            'description' => 'Updated job description.',
            'status' => JobPostingStatus::Published->value,
        ]);
    }

    public function test_employer_cannot_edit_another_employers_job_posting(): void
    {
        $employer = User::factory()->employer()->create([
            'is_active' => true,
            'is_blocked_from_posts' => false,
        ]);

        $otherEmployer = User::factory()->employer()->create([
            'is_active' => true,
            'is_blocked_from_posts' => false,
        ]);

        $job = JobPosting::factory()->create([
            'employer_id' => $otherEmployer->id,
        ]);

        $response = $this
            ->actingAs($employer)
            ->get(route('employer.jobs.edit', $job));

        $response->assertForbidden();
    }

    public function test_employer_cannot_update_another_employers_job_posting(): void
    {
        $employer = User::factory()->employer()->create([
            'is_active' => true,
            'is_blocked_from_posts' => false,
        ]);

        $otherEmployer = User::factory()->employer()->create([
            'is_active' => true,
            'is_blocked_from_posts' => false,
        ]);

        $job = JobPosting::factory()->create([
            'employer_id' => $otherEmployer->id,
        ]);

        $response = $this
            ->actingAs($employer)
            ->patch(route('employer.jobs.update', $job), [
                'title' => 'Unauthorized Update',
                'description' => 'This should not be allowed.',
                'status' => JobPostingStatus::Draft->value,
            ]);

        $response->assertForbidden();

        $this->assertDatabaseMissing('job_postings', [
            'id' => $job->id,
            'title' => 'Unauthorized Update',
        ]);
    }

    public function test_employer_cannot_delete_another_employers_job_posting(): void
    {
        $employer = User::factory()->employer()->create([
            'is_active' => true,
            'is_blocked_from_posts' => false,
        ]);

        $otherEmployer = User::factory()->employer()->create([
            'is_active' => true,
            'is_blocked_from_posts' => false,
        ]);

        $job = JobPosting::factory()->create([
            'employer_id' => $otherEmployer->id,
        ]);

        $response = $this
            ->actingAs($employer)
            ->delete(route('employer.jobs.destroy', $job));

        $response->assertForbidden();

        $this->assertDatabaseHas('job_postings', [
            'id' => $job->id,
        ]);
    }

    public function test_employer_can_delete_their_own_job_posting(): void
    {
        $employer = User::factory()->employer()->create([
            'is_active' => true,
            'is_blocked_from_posts' => false,
        ]);

        $job = JobPosting::factory()->create([
            'employer_id' => $employer->id,
        ]);

        $response = $this
            ->actingAs($employer)
            ->delete(route('employer.jobs.destroy', $job));

        $response
            ->assertRedirect(route('employer.jobs.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('job_postings', [
            'id' => $job->id,
        ]);
    }

    public function test_employer_cannot_create_job_posting_when_blocked(): void
    {
        $employer = User::factory()->employer()->create([
            'is_active' => true,
            'is_blocked_from_posts' => true,
        ]);

        $response = $this
            ->actingAs($employer)
            ->post(route('employer.jobs.store'), [
                'title' => 'Blocked Job',
                'description' => 'This should not be created.',
                'status' => JobPostingStatus::Draft->value,
            ]);

        $response->assertForbidden();

        $this->assertDatabaseMissing('job_postings', [
            'title' => 'Blocked Job',
        ]);
    }

    public function test_job_posting_creation_requires_valid_data(): void
    {
        $employer = User::factory()->employer()->create([
            'is_active' => true,
            'is_blocked_from_posts' => false,
        ]);

        $response = $this
            ->actingAs($employer)
            ->post(route('employer.jobs.store'), [
                'title' => '',
                'description' => '',
                'status' => 'invalid-status',
            ]);

        $response->assertSessionHasErrors([
            'title',
            'description',
            'status',
        ]);
    }
}
