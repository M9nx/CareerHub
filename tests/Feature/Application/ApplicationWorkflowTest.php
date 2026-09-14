<?php

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\JobPosting;
use App\Models\User;

test('employee can apply and employer can review then accept', function () {
    $employer = User::factory()->employer()->create();
    $job = JobPosting::factory()->for($employer, 'employer')->published()->create([
        'title' => 'Laravel Developer',
    ]);
    $employee = actingAsEmployee();

    $this->post(route('employee.applications.store'), [
        'job_posting_id' => $job->id,
        'cover_letter' => 'I would like to join the team.',
    ])
        ->assertRedirect(route('employee.applications.index'))
        ->assertSessionHas('success', __('Application submitted successfully.'));

    $application = Application::query()
        ->where('employee_id', $employee->id)
        ->where('job_posting_id', $job->id)
        ->first();

    expect($application)->not->toBeNull()
        ->and($application->status)->toBe(ApplicationStatus::Submitted)
        ->and($application->cover_letter)->toBe('I would like to join the team.');

    $this->get(route('employee.applications.index'))
        ->assertOk()
        ->assertSee('Laravel Developer')
        ->assertSee('Submitted');

    $this->actingAs($employer)
        ->get(route('employer.applications.index'))
        ->assertOk()
        ->assertSee($employee->name)
        ->assertSee('Laravel Developer');

    $this->from(route('employer.applications.index'))
        ->patch(route('employer.applications.update', $application), [
            'status' => ApplicationStatus::UnderReview->value,
        ])
        ->assertRedirect(route('employer.applications.index'))
        ->assertSessionHas('success', __('Application status updated successfully.'));

    expect($application->fresh()->status)->toBe(ApplicationStatus::UnderReview);

    $this->from(route('employer.applications.show', $application))
        ->patch(route('employer.applications.update', $application), [
            'status' => ApplicationStatus::Accepted->value,
        ])
        ->assertRedirect(route('employer.applications.show', $application));

    expect($application->fresh()->status)->toBe(ApplicationStatus::Accepted);
});

test('employer cannot skip from submitted to accepted', function () {
    $employer = actingAsEmployer();
    $job = JobPosting::factory()->for($employer, 'employer')->published()->create();
    $application = Application::factory()
        ->for($job, 'jobPosting')
        ->submitted()
        ->create();

    $this->patch(route('employer.applications.update', $application), [
        'status' => ApplicationStatus::Accepted->value,
    ])->assertForbidden();

    expect($application->fresh()->status)->toBe(ApplicationStatus::Submitted);
});

test('employer cannot review another employers application', function () {
    actingAsEmployer();
    $application = Application::factory()->submitted()->create();

    $this->get(route('employer.applications.show', $application))->assertForbidden();

    $this->patch(route('employer.applications.update', $application), [
        'status' => ApplicationStatus::UnderReview->value,
    ])->assertForbidden();

    $this->get(route('employer.applications.index'))
        ->assertOk()
        ->assertDontSee($application->employee->name);

    expect($application->fresh()->status)->toBe(ApplicationStatus::Submitted);
});

test('employee cannot access employer application review', function () {
    actingAsEmployee();
    $application = Application::factory()->submitted()->create();

    $this->get(route('employer.applications.index'))->assertForbidden();
    $this->get(route('employer.applications.show', $application))->assertForbidden();
    $this->patch(route('employer.applications.update', $application), [
        'status' => ApplicationStatus::UnderReview->value,
    ])->assertForbidden();
});

test('employee cannot apply twice to the same job', function () {
    $employee = actingAsEmployee();
    $job = JobPosting::factory()->published()->create();

    Application::factory()
        ->for($job, 'jobPosting')
        ->for($employee, 'employee')
        ->create();

    $this->from(route('employee.applications.index'))
        ->post(route('employee.applications.store'), [
            'job_posting_id' => $job->id,
        ])
        ->assertRedirect(route('employee.applications.index'))
        ->assertInvalid([
            'job_posting_id' => __('validation.unique', ['attribute' => 'job posting id']),
        ]);
});

test('employee cannot apply to an unpublished job', function () {
    actingAsEmployee();
    $job = JobPosting::factory()->draft()->create();

    $this->from(route('employee.applications.index'))
        ->post(route('employee.applications.store'), [
            'job_posting_id' => $job->id,
        ])
        ->assertRedirect(route('employee.applications.index'))
        ->assertInvalid([
            'job_posting_id' => __('validation.exists', ['attribute' => 'job posting id']),
        ]);

    $this->assertDatabaseMissing('applications', [
        'job_posting_id' => $job->id,
    ]);
});

test('invalid application status is rejected', function () {
    $employer = actingAsEmployer();
    $job = JobPosting::factory()->for($employer, 'employer')->published()->create();
    $application = Application::factory()
        ->for($job, 'jobPosting')
        ->submitted()
        ->create();

    $this->from(route('employer.applications.index'))
        ->patch(route('employer.applications.update', $application), [
            'status' => 'not-a-status',
        ])
        ->assertRedirect(route('employer.applications.index'))
        ->assertInvalid([
            'status' => __('validation.enum', ['attribute' => 'status']),
        ]);
});

test('guest is redirected to login from application routes', function () {
    $application = Application::factory()->submitted()->create();

    $this->get(route('employee.applications.index'))
        ->assertRedirect(route('login'));

    $this->post(route('employee.applications.store'), [
        'job_posting_id' => $application->job_posting_id,
    ])->assertRedirect(route('login'));

    $this->get(route('employer.applications.index'))
        ->assertRedirect(route('login'));
});

test('application details and listing escape user content', function () {
    $employer = actingAsEmployer();
    $job = JobPosting::factory()->for($employer, 'employer')->published()->create([
        'title' => '<script>alert("xss")</script>',
    ]);
    $employee = User::factory()->employee()->create([
        'name' => '<img src=x onerror=alert(1)>',
    ]);
    $application = Application::factory()
        ->for($job, 'jobPosting')
        ->for($employee, 'employee')
        ->create([
            'cover_letter' => '<script>alert("cover")</script>',
        ]);

    $this->get(route('employer.applications.index'))
        ->assertOk()
        ->assertDontSee('<script>alert("xss")</script>', false)
        ->assertDontSee('<img src=x onerror=alert(1)>', false);

    $this->get(route('employer.applications.show', $application))
        ->assertOk()
        ->assertDontSee('<script>alert("cover")</script>', false)
        ->assertSee('&lt;script&gt;alert(&quot;cover&quot;)&lt;/script&gt;', false);
});

test('application navigation uses named index routes', function () {
    actingAsEmployee();

    $this->get(route('employee.dashboard'))
        ->assertOk()
        ->assertSee(__('Applications'))
        ->assertSee(route('employee.applications.index'), false);

    actingAsEmployer();

    $this->get(route('employer.dashboard'))
        ->assertOk()
        ->assertSee(__('Applications'))
        ->assertSee(route('employer.applications.index'), false);
});
