<?php

use App\Models\EmployerProfile;
use App\Models\JobPosting;
use App\Models\User;

test('employee can browse published jobs with company name', function () {
    actingAsEmployee();

    $employer = User::factory()->employer()->has(
        EmployerProfile::factory()->state(['company_name' => 'Acme Hiring Co'])
    )->create();

    JobPosting::factory()->for($employer, 'employer')->published()->create([
        'title' => 'Published Career Role',
    ]);

    $this->get(route('employee.jobs.index'))
        ->assertOk()
        ->assertSee(__('Available Jobs'))
        ->assertSee('Published Career Role')
        ->assertSee('Acme Hiring Co');
});

test('employee can view a published job posting', function () {
    actingAsEmployee();

    $employer = User::factory()->employer()->has(
        EmployerProfile::factory()->state(['company_name' => 'Acme Hiring Co'])
    )->create();

    $job = JobPosting::factory()->for($employer, 'employer')->published()->create([
        'title' => 'Published Career Role',
        'description' => 'Build and maintain Laravel applications.',
    ]);

    $this->get(route('employee.jobs.show', $job))
        ->assertOk()
        ->assertSee('Published Career Role')
        ->assertSee('Acme Hiring Co')
        ->assertSee('Build and maintain Laravel applications.')
        ->assertSee(__('Apply'))
        ->assertSee(__('Back to Jobs'));
});

test('unpublished jobs are hidden from the employee listing', function (string $state, array $overrides) {
    actingAsEmployee();

    JobPosting::factory()->{$state}()->create([
        'title' => 'Hidden Career Role',
        ...$overrides,
    ]);

    $this->get(route('employee.jobs.index'))
        ->assertOk()
        ->assertDontSee('Hidden Career Role')
        ->assertSee(__('No jobs are currently available.'));
})->with([
    'draft' => ['draft', []],
    'closed' => ['closed', []],
    'archived' => ['archived', []],
    'inactive published' => ['published', ['is_active' => false]],
]);

test('unpublished jobs return 404 on the employee show page', function (string $state, array $overrides) {
    actingAsEmployee();

    $job = JobPosting::factory()->{$state}()->create([
        'title' => 'Hidden Career Role',
        ...$overrides,
    ]);

    $this->get(route('employee.jobs.show', $job))
        ->assertNotFound();
})->with([
    'draft' => ['draft', []],
    'closed' => ['closed', []],
    'archived' => ['archived', []],
    'inactive published' => ['published', ['is_active' => false]],
]);

test('guest is redirected to login from employee jobs', function () {
    $job = JobPosting::factory()->published()->create();

    $this->get(route('employee.jobs.index'))
        ->assertRedirect(route('login'));

    $this->get(route('employee.jobs.show', $job))
        ->assertRedirect(route('login'));
});

test('employer cannot access employee jobs', function () {
    $job = JobPosting::factory()->published()->create();

    actingAsEmployer();

    $this->get(route('employee.jobs.index'))->assertForbidden();
    $this->get(route('employee.jobs.show', $job))->assertForbidden();
});

test('employee navigation includes a jobs link', function () {
    actingAsEmployee();

    $this->get(route('employee.dashboard'))
        ->assertOk()
        ->assertSee(__('Jobs'))
        ->assertSee(route('employee.jobs.index'), false);
});

test('job posting titles are escaped on the employee listing', function () {
    actingAsEmployee();

    JobPosting::factory()->published()->create([
        'title' => '<script>alert("xss")</script>',
    ]);

    $this->get(route('employee.jobs.index'))
        ->assertOk()
        ->assertDontSee('<script>alert("xss")</script>', false)
        ->assertSee('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', false);
});

test('job posting title and description are escaped on the show page', function () {
    actingAsEmployee();

    $job = JobPosting::factory()->published()->create([
        'title' => '<script>alert("xss")</script>',
        'description' => '<img src=x onerror=alert(1)>',
    ]);

    $this->get(route('employee.jobs.show', $job))
        ->assertOk()
        ->assertDontSee('<script>alert("xss")</script>', false)
        ->assertSee('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', false)
        ->assertDontSee('<img src=x onerror=alert(1)>', false);
});
