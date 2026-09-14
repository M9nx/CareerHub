<?php

use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Models\Post;
use App\Models\User;

test('feed shows published employer and employee posts with role badges', function () {
    $employer = User::factory()->employer()->create(['name' => 'Employer Author']);
    $employee = actingAsEmployee(['name' => 'Employee Author']);

    Post::factory()->for($employer, 'author')->published()->create([
        'author_role' => UserRole::Employer,
        'title' => 'Employer Published Post',
        'body' => 'Employer post body.',
    ]);

    Post::factory()->for($employee, 'author')->published()->create([
        'author_role' => UserRole::Employee,
        'title' => 'Employee Published Post',
        'body' => 'Employee post body.',
    ]);

    $this->get(route('feed.index'))
        ->assertOk()
        ->assertSee('Employer Published Post')
        ->assertSee('Employee Published Post')
        ->assertSee('Employer Author')
        ->assertSee('Employee Author')
        ->assertSee(UserRole::Employer->label())
        ->assertSee(UserRole::Employee->label());
});

test('feed does not show unpublished or inactive posts', function () {
    $employee = actingAsEmployee();

    Post::factory()->for($employee, 'author')->create([
        'author_role' => UserRole::Employee,
        'title' => 'Draft Post',
        'status' => PostStatus::Draft,
    ]);

    Post::factory()->for($employee, 'author')->published()->create([
        'author_role' => UserRole::Employee,
        'title' => 'Inactive Post',
        'is_active' => false,
    ]);

    $this->get(route('feed.index'))
        ->assertOk()
        ->assertDontSee('Draft Post')
        ->assertDontSee('Inactive Post')
        ->assertSee(__('No published posts available.'));
});

test('feed is paginated and keeps empty state for total zero only', function () {
    $employee = actingAsEmployee();

    Post::factory()
        ->for($employee, 'author')
        ->published()
        ->count(11)
        ->sequence(fn ($sequence) => [
            'title' => sprintf('Paginated Post #%02d', $sequence->index + 1),
            'author_role' => UserRole::Employee,
            'created_at' => now()->subMinutes(11 - $sequence->index),
        ])
        ->create();

    $this->get(route('feed.index'))
        ->assertOk()
        ->assertSee('Paginated Post #11')
        ->assertDontSee('Paginated Post #01')
        ->assertSee('Go to page 2', false);

    $this->get(route('feed.index', ['page' => 2]))
        ->assertOk()
        ->assertSee('Paginated Post #01')
        ->assertDontSee('Paginated Post #11')
        ->assertDontSee(__('No published posts available.'));

    $this->get(route('feed.index', ['page' => 99]))
        ->assertOk()
        ->assertDontSee(__('No published posts available.'))
        ->assertSee('Go to page 2', false);
});
test('posts created through persona controllers appear on the shared feed', function () {
    actingAsEmployer(['name' => 'HTTP Feed Employer']);

    $this->post(route('employer.posts.store'), [
        'title' => 'HTTP Employer Feed Post',
        'body' => 'Created through the employer post controller.',
        'publish' => '1',
    ])->assertRedirect(route('employer.posts.index'));

    actingAsEmployee(['name' => 'HTTP Feed Employee']);

    $this->post(route('employee.posts.store'), [
        'title' => 'HTTP Employee Feed Post',
        'body' => 'Created through the employee post controller.',
        'publish' => '1',
    ])->assertRedirect(route('employee.posts.index'));

    $this->get(route('feed.index'))
        ->assertOk()
        ->assertSee('HTTP Employer Feed Post')
        ->assertSee('HTTP Employee Feed Post')
        ->assertSee('HTTP Feed Employer')
        ->assertSee('HTTP Feed Employee');
});

test('feed role badge uses author_role not the live user role', function () {
    $user = actingAsEmployer(['name' => 'Role Switch Author']);

    Post::factory()->for($user, 'author')->published()->create([
        'author_role' => UserRole::Employer,
        'title' => 'Snapshot Role Post',
        'body' => 'Role badge should stay Employer.',
    ]);

    $user->update(['role' => UserRole::Employee]);

    $this->get(route('feed.index'))
        ->assertOk()
        ->assertSee('Snapshot Role Post')
        ->assertSee(UserRole::Employer->label())
        ->assertDontSee(UserRole::Employee->label());
});
