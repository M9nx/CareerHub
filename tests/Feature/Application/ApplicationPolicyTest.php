<?php

namespace Tests\Feature\Application;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_create_application(): void
    {
        $employee = User::factory()->employee()->create();

        $this->assertTrue(
            $employee->can('create', Application::class)
        );
    }

    public function test_employee_can_cancel_own_application(): void
    {
        $employee = User::factory()->employee()->create();

        $application = Application::factory()->create([
            'employee_id' => $employee->id,
            'status' => ApplicationStatus::Submitted,
        ]);

        $this->assertTrue(
            $employee->can('cancel', $application)
        );
    }

    public function test_employee_cannot_cancel_someone_elses_application(): void
    {
        $employee = User::factory()->employee()->create();

        $application = Application::factory()->create([
            'status' => ApplicationStatus::Submitted,
        ]);

        $this->assertFalse(
            $employee->can('cancel', $application)
        );
    }

    public function test_employee_cannot_cancel_accepted_application(): void
    {
        $employee = User::factory()->employee()->create();

        $application = Application::factory()->create([
            'employee_id' => $employee->id,
            'status' => ApplicationStatus::Accepted,
        ]);

        $this->assertFalse(
            $employee->can('cancel', $application)
        );
    }

    public function test_employee_cannot_cancel_rejected_application(): void
    {
        $employee = User::factory()->employee()->create();

        $application = Application::factory()->create([
            'employee_id' => $employee->id,
            'status' => ApplicationStatus::Rejected,
        ]);

        $this->assertFalse(
            $employee->can('cancel', $application)
        );
    }

    public function test_employee_cannot_review_application(): void
    {
        $employee = User::factory()->employee()->create();
        $application = Application::factory()->create();

        $this->assertFalse(
            $employee->can('update', $application)
        );
    }

    public function test_employer_can_review_application_for_own_job(): void
    {
        $employer = User::factory()->employer()->create();

        $job = JobPosting::factory()->create([
            'employer_id' => $employer->id,
        ]);

        $application = Application::factory()->create([
            'job_posting_id' => $job->id,
        ]);

        $this->assertTrue(
            $employer->can('update', $application)
        );
    }

    public function test_employer_cannot_review_application_for_another_employers_job(): void
    {
        $employer = User::factory()->employer()->create();

        $application = Application::factory()->create();

        $this->assertFalse(
            $employer->can('update', $application)
        );
    }
}