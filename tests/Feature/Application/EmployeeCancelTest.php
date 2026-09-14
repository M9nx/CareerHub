<?php

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\JobPosting;

test('employee can cancel their submitted application', function () {
    $employee = actingAsEmployee();
    $application = Application::factory()
        ->for($employee, 'employee')
        ->submitted()
        ->create();

    $this->patch(route('employee.applications.cancel', $application))
        ->assertRedirect(route('employee.applications.index'))
        ->assertSessionHas('success', __('Application cancelled successfully.'));

    $application->refresh();

    expect($application->status)->toBe(ApplicationStatus::Cancelled)
        ->and($application->cancelled_at)->not->toBeNull();
});

test('employee cannot cancel an already cancelled application', function () {
    $employee = actingAsEmployee();
    $application = Application::factory()
        ->for($employee, 'employee')
        ->create([
            'status' => ApplicationStatus::Cancelled,
            'cancelled_at' => now(),
        ]);

    $this->patch(route('employee.applications.cancel', $application))
        ->assertForbidden();

    expect($application->fresh()->status)->toBe(ApplicationStatus::Cancelled);
});

test('employee cannot cancel an accepted application', function () {
    $employee = actingAsEmployee();
    $application = Application::factory()
        ->for($employee, 'employee')
        ->create([
            'status' => ApplicationStatus::Accepted,
        ]);

    $this->patch(route('employee.applications.cancel', $application))
        ->assertForbidden();

    expect($application->fresh()->status)->toBe(ApplicationStatus::Accepted)
        ->and($application->fresh()->cancelled_at)->toBeNull();
});

test('employee cannot cancel another employees application', function () {
    actingAsEmployee();
    $application = Application::factory()->submitted()->create();

    $this->patch(route('employee.applications.cancel', $application))
        ->assertForbidden();

    expect($application->fresh()->status)->toBe(ApplicationStatus::Submitted)
        ->and($application->fresh()->cancelled_at)->toBeNull();
});

test('cancelled applications do not show a cancel action', function () {
    $employee = actingAsEmployee();
    $job = JobPosting::factory()->published()->create([
        'title' => 'Hidden Cancel Role',
    ]);
    $application = Application::factory()
        ->for($employee, 'employee')
        ->for($job, 'jobPosting')
        ->create([
            'status' => ApplicationStatus::Cancelled,
            'cancelled_at' => now(),
        ]);

    $this->get(route('employee.applications.index'))
        ->assertOk()
        ->assertSee('Hidden Cancel Role')
        ->assertDontSee(route('employee.applications.cancel', $application), false);
});

test('employer cannot cancel an employee application', function () {
    actingAsEmployer();
    $application = Application::factory()->submitted()->create();

    $this->patch(route('employee.applications.cancel', $application))
        ->assertForbidden();

    expect($application->fresh()->status)->toBe(ApplicationStatus::Submitted);
});

test('guest is redirected to login when cancelling an application', function () {
    $application = Application::factory()->submitted()->create();

    $this->patch(route('employee.applications.cancel', $application))
        ->assertRedirect(route('login'));
});
