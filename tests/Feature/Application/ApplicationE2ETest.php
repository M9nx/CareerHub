<?php

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\JobPosting;
use App\Models\User;

test('employee can apply and employer can review then accept an application', function () {
    $employer = User::factory()->employer()->create();
    $job = JobPosting::factory()->for($employer, 'employer')->published()->create([
        'title' => 'Application E2E Role',
    ]);
    $employee = actingAsEmployee();

    $this->post(route('employee.applications.store'), [
        'job_posting_id' => $job->id,
        'cover_letter' => 'Excited about this opportunity.',
    ])->assertRedirect(route('employee.applications.index'));

    $application = Application::query()
        ->where('employee_id', $employee->id)
        ->where('job_posting_id', $job->id)
        ->firstOrFail();

    expect($application->status)->toBe(ApplicationStatus::Submitted);

    $this->actingAs($employer)
        ->from(route('employer.applications.index'))
        ->patch(route('employer.applications.update', $application), [
            'status' => ApplicationStatus::UnderReview->value,
        ])
        ->assertRedirect(route('employer.applications.index'));

    $this->from(route('employer.applications.show', $application))
        ->patch(route('employer.applications.update', $application), [
            'status' => ApplicationStatus::Accepted->value,
        ])
        ->assertRedirect(route('employer.applications.show', $application));

    expect($application->fresh()->status)->toBe(ApplicationStatus::Accepted);
});

test('employee can cancel a submitted application and cancellation is terminal', function () {
    $employee = actingAsEmployee();
    $application = Application::factory()
        ->for($employee, 'employee')
        ->submitted()
        ->create();

    $this->patch(route('employee.applications.cancel', $application))
        ->assertRedirect(route('employee.applications.index'));

    expect($application->fresh()->status)->toBe(ApplicationStatus::Cancelled)
        ->and($application->fresh()->cancelled_at)->not->toBeNull();

    $this->patch(route('employee.applications.cancel', $application))
        ->assertForbidden();

    expect($application->fresh()->status)->toBe(ApplicationStatus::Cancelled);
});

test('employer cannot change the status of a cancelled application', function () {
    $employer = actingAsEmployer();
    $job = JobPosting::factory()->for($employer, 'employer')->published()->create();
    $application = Application::factory()
        ->for($job, 'jobPosting')
        ->create([
            'status' => ApplicationStatus::Cancelled,
            'cancelled_at' => now(),
        ]);

    $this->patch(route('employer.applications.update', $application), [
        'status' => ApplicationStatus::UnderReview->value,
    ])->assertForbidden();

    expect($application->fresh()->status)->toBe(ApplicationStatus::Cancelled);
});
