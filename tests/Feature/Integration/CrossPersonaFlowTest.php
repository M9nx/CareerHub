<?php

use App\Enums\ApplicationStatus;
use App\Enums\JobPostingStatus;
use App\Models\Application;
use App\Models\JobPosting;
use App\Models\User;

test('employer registers, publishes a job, employee applies, and employer accepts', function () {
    $this->post('/register', [
        'name' => 'Acme Robotics',
        'email' => 'employer-flow@careerhub.test',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'Employer',
    ])->assertRedirect(route('feed.index'));

    $employer = User::query()->where('email', 'employer-flow@careerhub.test')->firstOrFail();

    $this->actingAs($employer);

    $this->post(route('employer.jobs.store'), [
        'title' => 'Integration Laravel Engineer',
        'description' => 'Ship features across the CareerHub platform.',
        'status' => JobPostingStatus::Draft->value,
    ])->assertRedirect(route('employer.jobs.index'));

    $job = JobPosting::query()
        ->where('employer_id', $employer->id)
        ->where('title', 'Integration Laravel Engineer')
        ->firstOrFail();

    expect($job->status)->toBe(JobPostingStatus::Draft);

    $this->post(route('employer.jobs.publish', $job))
        ->assertRedirect(route('employer.jobs.edit', $job));

    expect($job->fresh()->status)->toBe(JobPostingStatus::Published);

    $this->post(route('logout'));

    $this->post('/register', [
        'name' => 'Jordan Applicant',
        'email' => 'employee-flow@careerhub.test',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'Employee',
    ])->assertRedirect(route('feed.index'));

    $employee = User::query()->where('email', 'employee-flow@careerhub.test')->firstOrFail();

    $this->actingAs($employee);

    $this->get(route('employee.jobs.index'))
        ->assertOk()
        ->assertSee('Integration Laravel Engineer');

    $this->post(route('employee.applications.store'), [
        'job_posting_id' => $job->id,
        'cover_letter' => 'Ready to contribute on day one.',
    ])->assertRedirect(route('employee.applications.index'));

    $application = Application::query()
        ->where('employee_id', $employee->id)
        ->where('job_posting_id', $job->id)
        ->firstOrFail();

    expect($application->status)->toBe(ApplicationStatus::Submitted);

    $this->post(route('logout'));
    $this->actingAs($employer);

    $this->get(route('employer.applications.index'))
        ->assertOk()
        ->assertSee('Jordan Applicant')
        ->assertSee('Integration Laravel Engineer');

    $this->from(route('employer.applications.index'))
        ->patch(route('employer.applications.update', $application), [
            'status' => ApplicationStatus::UnderReview->value,
        ])
        ->assertRedirect(route('employer.applications.index'));

    expect($application->fresh()->status)->toBe(ApplicationStatus::UnderReview);

    $this->from(route('employer.applications.show', $application))
        ->patch(route('employer.applications.update', $application), [
            'status' => ApplicationStatus::Accepted->value,
        ])
        ->assertRedirect(route('employer.applications.show', $application));

    expect($application->fresh()->status)->toBe(ApplicationStatus::Accepted);
});
