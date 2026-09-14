<?php

namespace Tests\Feature\Application;

use App\Actions\TransitionApplicationStatus;
use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_application_workflow(): void
    {
        $employer = User::factory()->employer()->create();

        $job = JobPosting::factory()->published()->create([
            'employer_id' => $employer->id,
        ]);

        $employee = User::factory()->employee()->create();

        $application = Application::factory()->create([
            'job_posting_id' => $job->id,
            'employee_id' => $employee->id,
            'status' => ApplicationStatus::Submitted,
        ]);

        $action = app(TransitionApplicationStatus::class);

        $action->handle(
            $application,
            ApplicationStatus::UnderReview
        );

        $application->refresh();

        $this->assertSame(
            ApplicationStatus::UnderReview,
            $application->status
        );

        $action->handle(
            $application,
            ApplicationStatus::Accepted
        );

        $application->refresh();

        $this->assertSame(
            ApplicationStatus::Accepted,
            $application->status
        );
    }
}