<?php

use App\Enums\UserRole;
use App\Models\Post;

test('blocked employee can view feed but cannot create a post', function () {
    $blockedEmployee = actingAsEmployee(['is_blocked_from_posts' => true]);

    Post::factory()->published()->create([
        'author_role' => UserRole::Employer,
        'title' => 'Published Employer Post',
    ]);

    $this->get(route('feed.index'))
        ->assertOk()
        ->assertSee('Published Employer Post')
        ->assertDontSee(__('Create Post'));

    $this->get(route('employee.posts.create'))
        ->assertRedirect(route('feed.index'))
        ->assertSessionHas('error', __('You are blocked from creating posts.'));

    $this->from(route('feed.index'))
        ->followingRedirects()
        ->post(route('employee.posts.store'), [
            'title' => 'Blocked Post',
            'body' => 'This post should not be created.',
            'publish' => true,
        ])
        ->assertOk()
        ->assertSee(__('You are blocked from creating posts.'));

    $this->assertDatabaseMissing('posts', [
        'title' => 'Blocked Post',
        'author_id' => $blockedEmployee->id,
    ]);
});

test('blocked employer can view feed but cannot create a post', function () {
    $blockedEmployer = actingAsEmployer(['is_blocked_from_posts' => true]);

    Post::factory()->published()->create([
        'author_role' => UserRole::Employee,
        'title' => 'Published Employee Post',
    ]);

    $this->get(route('feed.index'))
        ->assertOk()
        ->assertSee('Published Employee Post')
        ->assertDontSee(__('Create Post'));

    $this->get(route('employer.posts.create'))
        ->assertRedirect(route('feed.index'))
        ->assertSessionHas('error', __('You are blocked from creating posts.'));

    $this->from(route('feed.index'))
        ->post(route('employer.posts.store'), [
            'title' => 'Blocked Employer Post',
            'body' => 'This post should not be created.',
            'publish' => true,
        ])
        ->assertRedirect(route('feed.index'))
        ->assertSessionHas('error', __('You are blocked from creating posts.'));

    $this->assertDatabaseMissing('posts', [
        'title' => 'Blocked Employer Post',
        'author_id' => $blockedEmployer->id,
    ]);
});

test('unblocked employee sees create post on the feed', function () {
    actingAsEmployee();

    $this->get(route('feed.index'))
        ->assertOk()
        ->assertSee(__('Create Post'))
        ->assertSee(route('employee.posts.create'), false);
});
