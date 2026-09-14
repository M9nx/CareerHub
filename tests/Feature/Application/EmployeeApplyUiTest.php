<?php

use App\Enums\ApplicationStatus;
use App\Models\JobPosting;

test('employee can apply from the job show page', function () {
    $employee = actingAsEmployee();

    $jobPosting = JobPosting::factory()
        ->published()
        ->create();

    $this->get(route('employee.jobs.show', $jobPosting))
        ->assertOk()
        ->assertSee(__('Apply'))
        ->assertSee('cover_letter')
        ->assertSee(route('employee.applications.store'), false);

    $this->post(route('employee.applications.store'), [
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
});
