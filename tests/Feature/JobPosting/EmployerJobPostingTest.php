<?php

use App\Enums\JobPostingStatus;
use App\Models\JobPosting;
use App\Models\User;

test('employer can view their job postings and not others', function () {
    $employer = actingAsEmployer();

    JobPosting::factory()->for($employer, 'employer')->create([
        'title' => 'Own Backend Role',
    ]);

    JobPosting::factory()->create([
        'title' => 'Other Employer Role',
    ]);

    $this->get(route('employer.jobs.index'))
        ->assertOk()
        ->assertSee('Own Backend Role')
        ->assertSee(__('Jobs'))
        ->assertDontSee('Other Employer Role');
});

test('employer can view the create job form', function () {
    actingAsEmployer();

    $this->get(route('employer.jobs.create'))
        ->assertOk()
        ->assertSee(__('Create Job Posting'));
});

test('employer can create a job posting', function () {
    $employer = actingAsEmployer();

    $this->post(route('employer.jobs.store'), [
        'title' => 'Laravel Developer',
        'description' => 'Build and maintain Laravel applications.',
        'status' => JobPostingStatus::Draft->value,
    ])
        ->assertRedirect(route('employer.jobs.index'))
        ->assertSessionHas('success', __('Job posting created successfully.'));

    $this->assertDatabaseHas('job_postings', [
        'employer_id' => $employer->id,
        'title' => 'Laravel Developer',
        'description' => 'Build and maintain Laravel applications.',
        'status' => JobPostingStatus::Draft->value,
    ]);
});

test('employer can edit and update their own job posting', function () {
    $employer = actingAsEmployer();
    $job = JobPosting::factory()->for($employer, 'employer')->draft()->create();

    $this->get(route('employer.jobs.edit', $job))
        ->assertOk()
        ->assertSee($job->title)
        ->assertSee(__('Publish'));

    $this->patch(route('employer.jobs.update', $job), [
        'title' => 'Updated Laravel Developer',
        'description' => 'Updated job description.',
        'status' => JobPostingStatus::Published->value,
    ])
        ->assertRedirect(route('employer.jobs.index'))
        ->assertSessionHas('success', __('Job posting updated successfully.'));

    $job->refresh();

    expect($job->title)->toBe('Updated Laravel Developer')
        ->and($job->description)->toBe('Updated job description.')
        ->and($job->status)->toBe(JobPostingStatus::Published)
        ->and($job->published_at)->not->toBeNull()
        ->and($job->employer_id)->toBe($employer->id);
});

test('employer cannot change employer id on update', function () {
    $employer = actingAsEmployer();
    $otherEmployer = User::factory()->employer()->create();
    $job = JobPosting::factory()->for($employer, 'employer')->create();

    $this->from(route('employer.jobs.edit', $job))
        ->patch(route('employer.jobs.update', $job), [
            'title' => $job->title,
            'description' => $job->description,
            'status' => $job->status->value,
            'employer_id' => $otherEmployer->id,
        ])
        ->assertRedirect(route('employer.jobs.edit', $job))
        ->assertSessionHasErrors('employer_id');

    expect($job->fresh()->employer_id)->toBe($employer->id);
});

test('employer cannot edit update or delete another employers job posting', function () {
    $employer = actingAsEmployer();
    $job = JobPosting::factory()->create([
        'title' => 'Other Employer Role',
    ]);

    $this->get(route('employer.jobs.edit', $job))->assertForbidden();

    $this->patch(route('employer.jobs.update', $job), [
        'title' => 'Unauthorized Update',
        'description' => 'This should not be allowed.',
        'status' => JobPostingStatus::Draft->value,
    ])->assertForbidden();

    $this->delete(route('employer.jobs.destroy', $job))->assertForbidden();

    $this->assertDatabaseHas('job_postings', [
        'id' => $job->id,
        'title' => 'Other Employer Role',
    ]);
});

test('employer can delete their own job posting', function () {
    $employer = actingAsEmployer();
    $job = JobPosting::factory()->for($employer, 'employer')->create();

    $this->delete(route('employer.jobs.destroy', $job))
        ->assertRedirect(route('employer.jobs.index'))
        ->assertSessionHas('success', __('Job posting deleted successfully.'));

    $this->assertDatabaseMissing('job_postings', [
        'id' => $job->id,
    ]);
});

test('blocked employer cannot create a job posting', function () {
    actingAsEmployer(['is_blocked_from_posts' => true]);

    $this->get(route('employer.jobs.create'))->assertForbidden();

    $this->post(route('employer.jobs.store'), [
        'title' => 'Blocked Job',
        'description' => 'This should not be created.',
        'status' => JobPostingStatus::Draft->value,
    ])->assertForbidden();

    $this->assertDatabaseMissing('job_postings', [
        'title' => 'Blocked Job',
    ]);
});

test('job posting creation requires valid data', function () {
    actingAsEmployer();

    $this->from(route('employer.jobs.create'))
        ->post(route('employer.jobs.store'), [
            'title' => '',
            'description' => '',
            'status' => 'invalid-status',
        ])
        ->assertRedirect(route('employer.jobs.create'))
        ->assertInvalid([
            'title' => __('validation.required', ['attribute' => 'title']),
            'description' => __('validation.required', ['attribute' => 'description']),
            'status' => __('validation.enum', ['attribute' => 'status']),
        ]);
});

test('guest is redirected to login from employer jobs', function () {
    $this->get(route('employer.jobs.index'))
        ->assertRedirect(route('login'));

    $this->post(route('employer.jobs.store'), [
        'title' => 'Guest Job',
        'description' => 'Guests cannot create jobs.',
        'status' => JobPostingStatus::Draft->value,
    ])->assertRedirect(route('login'));
});

test('employee cannot access employer jobs', function () {
    actingAsEmployee();

    $this->get(route('employer.jobs.index'))->assertForbidden();

    $this->post(route('employer.jobs.store'), [
        'title' => 'Employee Job',
        'description' => 'Employees cannot create employer jobs.',
        'status' => JobPostingStatus::Draft->value,
    ])->assertForbidden();
});

test('job posting titles are escaped on the index', function () {
    $employer = actingAsEmployer();

    JobPosting::factory()->for($employer, 'employer')->create([
        'title' => '<script>alert("xss")</script>',
    ]);

    $this->get(route('employer.jobs.index'))
        ->assertOk()
        ->assertDontSee('<script>alert("xss")</script>', false)
        ->assertSee('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', false);
});
