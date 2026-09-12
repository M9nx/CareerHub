<?php

namespace Tests\Feature\Application;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_application_factory_persists_with_submitted_status(): void
    {
        $application = Application::factory()->create();

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'status' => 'submitted',
        ]);
    }

    public function test_status_is_cast_to_application_status_enum(): void
    {
        $application = Application::factory()->create();

        $this->assertInstanceOf(
            ApplicationStatus::class,
            $application->status
        );

        $this->assertEquals(
            ApplicationStatus::Submitted,
            $application->status
        );
    }

    public function test_application_belongs_to_job_posting(): void
    {
        $application = Application::factory()->create();

        $this->assertNotNull($application->jobPosting);
        $this->assertEquals(
            $application->job_posting_id,
            $application->jobPosting->id
        );
    }

    public function test_application_belongs_to_employee(): void
    {
        $application = Application::factory()->create();

        $this->assertNotNull($application->employee);
        $this->assertEquals(
            $application->employee_id,
            $application->employee->id
        );
    }
}
