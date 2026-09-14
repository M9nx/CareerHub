<?php

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\JobPosting;

test('employee can apply from the job show page', function () {
    $employee = actingAsEmployee();

    $jobPosting = JobPosting::factory()
        ->published()
        ->create([
            'title' => 'Frontend Engineer',
        ]);

    $this->get(route('employee.jobs.show', $jobPosting))
        ->assertOk()
        ->assertSee(__('Apply'))
        ->assertSee('cover_letter', false)
        ->assertSee(route('employee.applications.store'), false);

    $this->from(route('employee.jobs.show', $jobPosting))
        ->post(route('employee.applications.store'), [
            'job_posting_id' => $jobPosting->id,
            'cover_letter' => 'I would like to apply for this position.',
        ])
        ->assertRedirect(route('employee.applications.index'))
        ->assertSessionHas(
            'success',
            __('Application submitted successfully.')
        );

    $this->assertDatabaseHas('applications', [
        'employee_id' => $employee->id,
        'job_posting_id' => $jobPosting->id,
        'cover_letter' => 'I would like to apply for this position.',
        'status' => ApplicationStatus::Submitted->value,
    ]);

    $this->get(route('employee.applications.index'))
        ->assertOk()
        ->assertSee('Frontend Engineer')
        ->assertSee('Submitted')
        ->assertSee(__('Application submitted successfully.'));
});

test('status badge renders under review as a headline label', function () {
    $employee = actingAsEmployee();
    $application = Application::factory()
        ->for($employee, 'employee')
        ->create([
            'status' => ApplicationStatus::UnderReview,
        ]);

    $this->get(route('employee.applications.index'))
        ->assertOk()
        ->assertSee($application->jobPosting->title)
        ->assertSee('Under Review')
        ->assertDontSee('Under review');
});
