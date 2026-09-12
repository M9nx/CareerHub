<?php

namespace Tests\Feature\JobPosting;

use App\Enums\JobPostingStatus;
use App\Enums\UserRole;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobPostingPolicyTest extends TestCase
{
    use RefreshDatabase;

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

        $jobPosting = JobPosting::factory()->create([
            'employer_id' => $otherEmployer->id,
        ]);

        $this->assertFalse(
            $employer->can('update', $jobPosting)
        );
    }

    public function test_employer_can_update_and_delete_their_own_job_posting(): void
    {
        $employer = User::factory()->employer()->create([
            'is_active' => true,
            'is_blocked_from_posts' => false,
        ]);

        $jobPosting = JobPosting::factory()->create([
            'employer_id' => $employer->id,
        ]);

        $this->assertTrue($employer->can('update', $jobPosting));
        $this->assertTrue($employer->can('delete', $jobPosting));
    }

    public function test_blocked_employer_cannot_create_job_posting(): void
    {
        $employer = User::factory()->employer()->create([
            'is_active' => true,
            'is_blocked_from_posts' => true,
        ]);

        $this->assertFalse($employer->can('create', JobPosting::class));
    }

    public function test_employee_can_only_view_published_active_job_postings(): void
    {
        $employee = User::factory()->employee()->create();

        $publishedActive = JobPosting::factory()->create([
            'status' => JobPostingStatus::Published,
            'is_active' => true,
        ]);

        $publishedInactive = JobPosting::factory()->create([
            'status' => JobPostingStatus::Published,
            'is_active' => false,
        ]);

        $draftActive = JobPosting::factory()->create([
            'status' => JobPostingStatus::Draft,
            'is_active' => true,
        ]);

        $this->assertTrue($employee->can('view', $publishedActive));
        $this->assertFalse($employee->can('view', $publishedInactive));
        $this->assertFalse($employee->can('view', $draftActive));
    }

    public function test_super_admin_can_perform_all_job_posting_actions(): void
    {
        $superAdmin = User::factory()->create([
            'role' => UserRole::SuperAdmin,
            'is_active' => false,
            'is_blocked_from_posts' => true,
        ]);

        $employer = User::factory()->employer()->create();

        $jobPosting = JobPosting::factory()->create([
            'employer_id' => $employer->id,
        ]);

        $this->assertTrue($superAdmin->can('viewAny', JobPosting::class));
        $this->assertTrue($superAdmin->can('view', $jobPosting));
        $this->assertTrue($superAdmin->can('create', JobPosting::class));
        $this->assertTrue($superAdmin->can('update', $jobPosting));
        $this->assertTrue($superAdmin->can('delete', $jobPosting));
    }
}
