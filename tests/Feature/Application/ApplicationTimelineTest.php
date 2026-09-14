<?php

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\User;

test('employee can view application timeline details', function () {
    $employee = actingAsEmployee();
    $application = Application::factory()
        ->for($employee, 'employee')
        ->submitted()
        ->create();

    $this->get(route('employee.applications.show', $application))
        ->assertOk()
        ->assertSee($application->jobPosting->title)
        ->assertSee(__('Application Timeline'))
        ->assertSee(__('Submitted'))
        ->assertDontSee(__('Cancelled at:'));
});

test('cancelled application timeline shows cancelled label and timestamp', function () {
    $employee = actingAsEmployee();
    $cancelledAt = now()->setSecond(0);
    $application = Application::factory()
        ->for($employee, 'employee')
        ->create([
            'status' => ApplicationStatus::Cancelled,
            'cancelled_at' => $cancelledAt,
        ]);

    $this->get(route('employee.applications.show', $application))
        ->assertOk()
        ->assertSee(__('Cancelled'))
        ->assertSee(__('Cancelled at:'))
        ->assertSee($cancelledAt->toDayDateTimeString());
});

test('employee cannot view another employees application timeline', function () {
    actingAsEmployee();
    $application = Application::factory()->submitted()->create();

    $this->get(route('employee.applications.show', $application))
        ->assertForbidden();
});

test('applications index links to the timeline show page', function () {
    $employee = actingAsEmployee();
    $application = Application::factory()
        ->for($employee, 'employee')
        ->submitted()
        ->create();

    $this->get(route('employee.applications.index'))
        ->assertOk()
        ->assertSee(__('View'))
        ->assertSee(route('employee.applications.show', $application), false);
});

test('employer cannot view employee application timeline', function () {
    $employer = User::factory()->employer()->create();
    $application = Application::factory()->submitted()->create();

    $this->actingAs($employer)
        ->get(route('employee.applications.show', $application))
        ->assertForbidden();
});
