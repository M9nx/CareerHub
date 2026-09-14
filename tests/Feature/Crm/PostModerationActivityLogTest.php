<?php

use App\Actions\BlockUserFromPosts;
use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Models\ActivityLog;
use App\Models\Post;
use App\Models\User;

test('hiding a post writes a hidden activity log', function () {
    $admin = User::factory()->superAdmin()->create();
    $author = User::factory()->employer()->create();
    $this->actingAs($admin);

    $post = Post::factory()->for($author, 'author')->published()->create([
        'title' => 'Visible community post',
        'author_role' => UserRole::Employer,
    ]);

    $post->update(['status' => PostStatus::Hidden]);

    $this->assertDatabaseHas('activity_logs', [
        'log_name' => 'post.hidden',
        'subject_type' => $post->getMorphClass(),
        'subject_id' => $post->id,
        'causer_id' => $admin->id,
    ]);

    $log = ActivityLog::query()
        ->where('log_name', 'post.hidden')
        ->where('subject_id', $post->id)
        ->first();

    expect($log->properties)->toMatchArray([
        'event' => 'hidden',
        'status' => PostStatus::Hidden->value,
        'from_status' => PostStatus::Published->value,
        'title' => 'Visible community post',
        'author_id' => $author->id,
    ]);
});

test('publishing a post writes a published activity log', function () {
    $author = actingAsEmployer();
    $post = Post::factory()->for($author, 'author')->create([
        'title' => 'Draft community post',
        'author_role' => UserRole::Employer,
    ]);

    $post->update(['status' => PostStatus::Published]);

    $this->assertDatabaseHas('activity_logs', [
        'log_name' => 'post.published',
        'subject_type' => $post->getMorphClass(),
        'subject_id' => $post->id,
        'causer_id' => $author->id,
    ]);

    $log = ActivityLog::query()
        ->where('log_name', 'post.published')
        ->where('subject_id', $post->id)
        ->first();

    expect($log->properties)->toMatchArray([
        'event' => 'published',
        'status' => PostStatus::Published->value,
        'from_status' => PostStatus::Draft->value,
        'title' => 'Draft community post',
        'author_id' => $author->id,
    ]);
});

test('updating a post title without a status change does not write a lifecycle log', function () {
    $author = actingAsEmployer();
    $post = Post::factory()->for($author, 'author')->published()->create([
        'title' => 'Original community title',
        'author_role' => UserRole::Employer,
    ]);

    ActivityLog::query()->delete();

    $post->update(['title' => 'Renamed community title']);

    $this->assertDatabaseMissing('activity_logs', [
        'log_name' => 'post.published',
        'subject_id' => $post->id,
    ]);

    $this->assertDatabaseMissing('activity_logs', [
        'log_name' => 'post.hidden',
        'subject_id' => $post->id,
    ]);

    $this->assertDatabaseCount('activity_logs', 0);
});

test('blocking a user from posts writes an activity log', function () {
    $admin = User::factory()->superAdmin()->create();
    $this->actingAs($admin);
    $user = User::factory()->employee()->create();

    app(BlockUserFromPosts::class)->handle($user, true, $admin);

    expect($user->fresh()->isBlockedFromPosts())->toBeTrue();

    $this->assertDatabaseHas('activity_logs', [
        'log_name' => 'user.blocked_from_posts',
        'subject_type' => $user->getMorphClass(),
        'subject_id' => $user->id,
        'causer_id' => $admin->id,
    ]);

    $log = ActivityLog::query()
        ->where('log_name', 'user.blocked_from_posts')
        ->where('subject_id', $user->id)
        ->first();

    expect($log->properties)->toMatchArray([
        'event' => 'blocked_from_posts',
        'is_blocked_from_posts' => true,
    ]);
});

test('unblocking a user from posts clears the flag without writing a blocked log', function () {
    $admin = User::factory()->superAdmin()->create();
    $this->actingAs($admin);
    $user = User::factory()->employee()->create([
        'is_blocked_from_posts' => true,
    ]);

    app(BlockUserFromPosts::class)->handle($user, false, $admin);

    expect($user->fresh()->isBlockedFromPosts())->toBeFalse();

    $this->assertDatabaseMissing('activity_logs', [
        'log_name' => 'user.blocked_from_posts',
        'subject_id' => $user->id,
    ]);
});

test('blocking an already blocked user does not write a duplicate log', function () {
    $admin = User::factory()->superAdmin()->create();
    $this->actingAs($admin);
    $user = User::factory()->employee()->create([
        'is_blocked_from_posts' => true,
    ]);

    app(BlockUserFromPosts::class)->handle($user, true, $admin);

    $this->assertDatabaseCount('activity_logs', 0);
});
