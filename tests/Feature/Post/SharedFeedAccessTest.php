<?php

use App\Enums\PostStatus;
use App\Models\Post;

test('guest is redirected to login from the feed', function () {
    $this->get(route('feed.index'))
        ->assertRedirect(route('login'));
});

test('employer can access the feed', function () {
    actingAsEmployer();

    $this->get(route('feed.index'))
        ->assertOk()
        ->assertSee(__('Feed'))
        ->assertSee(__('No published posts available.'));
});

test('employee can access the feed', function () {
    actingAsEmployee();

    $this->get(route('feed.index'))
        ->assertOk()
        ->assertSee(__('Feed'))
        ->assertSee(__('No published posts available.'));
});

test('feed displays published posts', function () {
    $employee = actingAsEmployee();

    Post::factory()->for($employee, 'author')->published()->create([
        'title' => 'Published Career Post',
        'body' => 'Published career body',
        'author_role' => $employee->role,
    ]);

    $this->get(route('feed.index'))
        ->assertOk()
        ->assertSee('Published Career Post')
        ->assertSee('Published career body')
        ->assertSee($employee->name);
});

test('feed hides unpublished posts', function (PostStatus $status) {
    $employee = actingAsEmployee();

    Post::factory()->for($employee, 'author')->create([
        'title' => 'Unpublished Career Post',
        'status' => $status,
        'author_role' => $employee->role,
    ]);

    $this->get(route('feed.index'))
        ->assertOk()
        ->assertDontSee('Unpublished Career Post')
        ->assertSee(__('No published posts available.'));
})->with([
    'draft' => PostStatus::Draft,
    'hidden' => PostStatus::Hidden,
    'archived' => PostStatus::Archived,
]);

test('feed hides inactive published posts', function () {
    $employee = actingAsEmployee();

    Post::factory()->for($employee, 'author')->published()->create([
        'title' => 'Inactive Career Post',
        'is_active' => false,
        'author_role' => $employee->role,
    ]);

    $this->get(route('feed.index'))
        ->assertOk()
        ->assertDontSee('Inactive Career Post')
        ->assertSee(__('No published posts available.'));
});

test('feed escapes post title and body', function () {
    $employee = actingAsEmployee();

    Post::factory()->for($employee, 'author')->published()->create([
        'title' => '<script>alert("xss")</script>',
        'body' => '<img src=x onerror=alert(1)>',
        'author_role' => $employee->role,
    ]);

    $this->get(route('feed.index'))
        ->assertOk()
        ->assertDontSee('<script>alert("xss")</script>', false)
        ->assertSee('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', false)
        ->assertDontSee('<img src=x onerror=alert(1)>', false);
});
