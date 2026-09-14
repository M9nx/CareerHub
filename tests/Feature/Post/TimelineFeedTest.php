<?php

use App\Enums\ApplicationStatus;
use App\Enums\JobPostingStatus;
use App\Enums\PostStatus;
use App\Models\Application;
use App\Models\JobPosting;
use App\Models\Post;
use App\Models\User;

test('guest is redirected to login from the timeline feed', function () {
    $this->get(route('feed.index'))
        ->assertRedirect(route('login'));
});

test('timeline shows mixed items in chronological order newest first', function () {
    $employer = User::factory()->employer()->create(['name' => 'Timeline Employer']);
    $employee = actingAsEmployee(['name' => 'Timeline Employee']);

    $post = Post::factory()->for($employer, 'author')->published()->create([
        'title' => 'Oldest Timeline Post',
        'author_role' => $employer->role,
        'created_at' => now()->subDays(3),
        'updated_at' => now()->subDays(3),
    ]);

    $job = JobPosting::factory()->for($employer, 'employer')->published()->create([
        'title' => 'Middle Timeline Job',
        'published_at' => now()->subDays(2),
        'created_at' => now()->subDays(2),
        'updated_at' => now()->subDays(2),
    ]);

    Application::factory()
        ->for($job, 'jobPosting')
        ->for($employee, 'employee')
        ->create([
            'status' => ApplicationStatus::Submitted,
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

    $response = $this->get(route('feed.index'))->assertOk();

    $content = $response->getContent();
    $applicationPosition = strpos($content, 'Application update');
    $jobPosition = strpos($content, 'Middle Timeline Job');
    $postPosition = strpos($content, 'Oldest Timeline Post');

    expect($applicationPosition)->not->toBeFalse()
        ->and($jobPosition)->not->toBeFalse()
        ->and($postPosition)->not->toBeFalse()
        ->and($applicationPosition)->toBeLessThan($jobPosition)
        ->and($jobPosition)->toBeLessThan($postPosition);
});

test('timeline omits hidden posts and draft jobs', function () {
    $employer = User::factory()->employer()->create();
    actingAsEmployee();

    Post::factory()->for($employer, 'author')->published()->create([
        'title' => 'Visible Timeline Post',
        'author_role' => $employer->role,
    ]);

    Post::factory()->for($employer, 'author')->create([
        'title' => 'Hidden Timeline Post',
        'status' => PostStatus::Hidden,
        'author_role' => $employer->role,
    ]);

    JobPosting::factory()->for($employer, 'employer')->published()->create([
        'title' => 'Visible Timeline Job',
    ]);

    JobPosting::factory()->for($employer, 'employer')->create([
        'title' => 'Draft Timeline Job',
        'status' => JobPostingStatus::Draft,
        'published_at' => null,
    ]);

    $this->get(route('feed.index'))
        ->assertOk()
        ->assertSee('Visible Timeline Post')
        ->assertSee('Visible Timeline Job')
        ->assertDontSee('Hidden Timeline Post')
        ->assertDontSee('Draft Timeline Job');
});

test('employer sees application events for own job postings only', function () {
    $employer = actingAsEmployer(['name' => 'Own Jobs Employer']);
    $otherEmployer = User::factory()->employer()->create(['name' => 'Other Jobs Employer']);
    $employee = User::factory()->employee()->create(['name' => 'Applying Employee']);

    $ownJob = JobPosting::factory()->for($employer, 'employer')->published()->create([
        'title' => 'Own Employer Job',
    ]);

    $otherJob = JobPosting::factory()->for($otherEmployer, 'employer')->published()->create([
        'title' => 'Other Employer Job',
    ]);

    Application::factory()
        ->for($ownJob, 'jobPosting')
        ->for($employee, 'employee')
        ->create([
            'status' => ApplicationStatus::UnderReview,
        ]);

    Application::factory()
        ->for($otherJob, 'jobPosting')
        ->for($employee, 'employee')
        ->create([
            'status' => ApplicationStatus::Submitted,
        ]);

    $response = $this->get(route('feed.index'))->assertOk();

    $response
        ->assertSee('Own Employer Job')
        ->assertSee('Other Employer Job')
        ->assertSee('Applying Employee')
        ->assertSee('Under Review')
        ->assertDontSee('Submitted');

    expect(substr_count($response->getContent(), 'Application update'))->toBe(1);
});

test('employee sees only their own application events', function () {
    $employer = User::factory()->employer()->create();
    $employee = actingAsEmployee(['name' => 'Primary Employee']);
    $otherEmployee = User::factory()->employee()->create(['name' => 'Other Employee']);

    $job = JobPosting::factory()->for($employer, 'employer')->published()->create([
        'title' => 'Shared Job Posting',
    ]);

    Application::factory()
        ->for($job, 'jobPosting')
        ->for($employee, 'employee')
        ->create([
            'status' => ApplicationStatus::Submitted,
        ]);

    Application::factory()
        ->for($job, 'jobPosting')
        ->for($otherEmployee, 'employee')
        ->create([
            'status' => ApplicationStatus::Accepted,
        ]);

    $this->get(route('feed.index'))
        ->assertOk()
        ->assertSee('Shared Job Posting')
        ->assertSee('Your application')
        ->assertSee('Submitted')
        ->assertDontSee('Other Employee')
        ->assertDontSee('Accepted');
});
