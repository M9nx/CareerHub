<?php

use App\Enums\JobPostingStatus;
use App\Models\JobPosting;

test('employer can publish their own draft job posting', function () {
    $employer = actingAsEmployer();
    $job = JobPosting::factory()->for($employer, 'employer')->draft()->create();

    $this->post(route('employer.jobs.publish', $job))
        ->assertRedirect(route('employer.jobs.edit', $job))
        ->assertSessionHas('success', __('Job posting published successfully.'));

    $job->refresh();

    expect($job->status)->toBe(JobPostingStatus::Published)
        ->and($job->published_at)->not->toBeNull();
});

test('employer can close their own published job posting', function () {
    $employer = actingAsEmployer();
    $job = JobPosting::factory()->for($employer, 'employer')->published()->create();
    $publishedAt = $job->published_at;

    $this->post(route('employer.jobs.close', $job))
        ->assertRedirect(route('employer.jobs.edit', $job))
        ->assertSessionHas('success', __('Job posting closed successfully.'));

    $job->refresh();

    expect($job->status)->toBe(JobPostingStatus::Closed)
        ->and($job->published_at?->equalTo($publishedAt))->toBeTrue();
});

test('draft edit page shows the publish action', function () {
    $employer = actingAsEmployer();
    $job = JobPosting::factory()->for($employer, 'employer')->draft()->create();

    $this->get(route('employer.jobs.edit', $job))
        ->assertOk()
        ->assertSee(__('Publish'))
        ->assertSee(route('employer.jobs.publish', $job), false)
        ->assertDontSee(route('employer.jobs.close', $job), false);
});

test('published edit page shows the close action', function () {
    $employer = actingAsEmployer();
    $job = JobPosting::factory()->for($employer, 'employer')->published()->create();

    $this->get(route('employer.jobs.edit', $job))
        ->assertOk()
        ->assertSee(__('Close'))
        ->assertSee(route('employer.jobs.close', $job), false)
        ->assertDontSee(route('employer.jobs.publish', $job), false);
});

test('employer cannot publish a job posting that is not a draft', function (string $state) {
    $employer = actingAsEmployer();
    $job = JobPosting::factory()->for($employer, 'employer')->{$state}()->create();
    $status = $job->status;
    $publishedAt = $job->published_at;

    $this->post(route('employer.jobs.publish', $job))->assertForbidden();

    $job->refresh();

    expect($job->status)->toBe($status)
        ->and($job->published_at?->toJSON())->toBe($publishedAt?->toJSON());
})->with([
    'published',
    'closed',
    'archived',
]);

test('employer cannot close a job posting that is not published', function (string $state) {
    $employer = actingAsEmployer();
    $job = JobPosting::factory()->for($employer, 'employer')->{$state}()->create();
    $status = $job->status;

    $this->post(route('employer.jobs.close', $job))->assertForbidden();

    expect($job->fresh()->status)->toBe($status);
})->with([
    'draft',
    'closed',
    'archived',
]);

test('employer cannot transition another employers job posting', function (string $state, string $routeName) {
    actingAsEmployer();
    $job = JobPosting::factory()->{$state}()->create();
    $status = $job->status;

    $this->post(route($routeName, $job))->assertForbidden();

    expect($job->fresh()->status)->toBe($status);
})->with([
    'publish' => ['draft', 'employer.jobs.publish'],
    'close' => ['published', 'employer.jobs.close'],
]);

test('blocked employer cannot transition a job posting', function (string $state, string $routeName) {
    $employer = actingAsEmployer(['is_blocked_from_posts' => true]);
    $job = JobPosting::factory()->for($employer, 'employer')->{$state}()->create();
    $status = $job->status;

    $this->post(route($routeName, $job))->assertForbidden();

    expect($job->fresh()->status)->toBe($status);
})->with([
    'publish' => ['draft', 'employer.jobs.publish'],
    'close' => ['published', 'employer.jobs.close'],
]);

test('guest is redirected to login from job transitions', function (string $state, string $routeName) {
    $job = JobPosting::factory()->{$state}()->create();

    $this->post(route($routeName, $job))
        ->assertRedirect(route('login'));
})->with([
    'publish' => ['draft', 'employer.jobs.publish'],
    'close' => ['published', 'employer.jobs.close'],
]);

test('employee cannot transition employer job postings', function (string $state, string $routeName) {
    actingAsEmployee();
    $job = JobPosting::factory()->{$state}()->create();
    $status = $job->status;

    $this->post(route($routeName, $job))->assertForbidden();

    expect($job->fresh()->status)->toBe($status);
})->with([
    'publish' => ['draft', 'employer.jobs.publish'],
    'close' => ['published', 'employer.jobs.close'],
]);
