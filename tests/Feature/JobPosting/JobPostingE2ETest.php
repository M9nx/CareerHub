<?php

use App\Enums\JobPostingStatus;
use App\Models\JobPosting;

test('job posting moves from draft to published to browsable to closed', function () {
    $employer = actingAsEmployer();

    $this->post(route('employer.jobs.store'), [
        'title' => 'Lifecycle Backend Engineer',
        'description' => 'Own the full job posting lifecycle.',
        'status' => JobPostingStatus::Draft->value,
    ])->assertRedirect(route('employer.jobs.index'));

    $job = JobPosting::query()
        ->where('employer_id', $employer->id)
        ->where('title', 'Lifecycle Backend Engineer')
        ->firstOrFail();

    expect($job->status)->toBe(JobPostingStatus::Draft);

    $this->post(route('employer.jobs.publish', $job))
        ->assertRedirect(route('employer.jobs.edit', $job));

    expect($job->fresh()->status)->toBe(JobPostingStatus::Published);

    actingAsEmployee();

    $this->get(route('employee.jobs.index'))
        ->assertOk()
        ->assertSee('Lifecycle Backend Engineer');

    $this->get(route('employee.jobs.show', $job))
        ->assertOk()
        ->assertSee('Lifecycle Backend Engineer')
        ->assertSee('Own the full job posting lifecycle.');

    $this->actingAs($employer);

    $this->post(route('employer.jobs.close', $job))
        ->assertRedirect(route('employer.jobs.edit', $job));

    expect($job->fresh()->status)->toBe(JobPostingStatus::Closed);

    actingAsEmployee();

    $this->get(route('employee.jobs.index'))
        ->assertOk()
        ->assertDontSee('Lifecycle Backend Engineer');

    $this->get(route('employee.jobs.show', $job))
        ->assertNotFound();
});
