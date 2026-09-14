<?php

namespace Tests\Feature\Application;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeCancelTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_cancel_application(): void
    {
        $employee = User::factory()->employee()->create();

        $application = Application::factory()->create([
            'employee_id' => $employee->id,
            'status' => ApplicationStatus::Submitted,
        ]);

        $this->actingAs($employee)
            ->patch(
                route('employee.applications.cancel', $application)
            )
            ->assertRedirect();

        $application->refresh();

        $this->assertSame(
            ApplicationStatus::Cancelled,
            $application->status
        );

        $this->assertNotNull(
            $application->cancelled_at
        );
    }
}