<?php

use App\Models\Post;
use App\Models\PostComment;
use App\Models\User;

test('authenticated user can comment on a published feed post', function () {
    $author = User::factory()->employer()->create();
    $viewer = actingAsEmployee(['name' => 'Commenter One']);
    $post = Post::factory()->for($author, 'author')->published()->create([
        'body' => 'Post awaiting discussion.',
        'author_role' => $author->role,
    ]);

    $this->from(route('feed.index'))
        ->post(route('feed.posts.comments.store', $post), [
            'body' => 'Thoughtful reply on the feed.',
        ])
        ->assertRedirect(route('feed.index').'#post-'.$post->id);

    $this->assertDatabaseHas('post_comments', [
        'post_id' => $post->id,
        'user_id' => $viewer->id,
        'body' => 'Thoughtful reply on the feed.',
    ]);

    $this->get(route('feed.index'))
        ->assertOk()
        ->assertSee('Thoughtful reply on the feed.')
        ->assertSee('Commenter One');
});

test('comment body is required and capped', function () {
    $author = User::factory()->employee()->create();
    $viewer = actingAsEmployee();
    $post = Post::factory()->for($author, 'author')->published()->create([
        'author_role' => $author->role,
    ]);

    $this->from(route('feed.index'))
        ->post(route('feed.posts.comments.store', $post), [
            'body' => '',
        ])
        ->assertSessionHasErrors('body');

    $this->from(route('feed.index'))
        ->post(route('feed.posts.comments.store', $post), [
            'body' => str_repeat('x', 2001),
        ])
        ->assertSessionHasErrors('body');

    expect(PostComment::query()->where('post_id', $post->id)->exists())->toBeFalse();
});

test('comment author can delete their own comment', function () {
    $author = User::factory()->employee()->create();
    $commenter = actingAsEmployee();
    $post = Post::factory()->for($author, 'author')->published()->create([
        'author_role' => $author->role,
    ]);
    $comment = PostComment::factory()->for($post)->for($commenter, 'user')->create([
        'body' => 'Temporary comment',
    ]);

    $this->from(route('feed.index'))
        ->delete(route('feed.posts.comments.destroy', [$post, $comment]))
        ->assertRedirect(route('feed.index').'#post-'.$post->id);

    $this->assertDatabaseMissing('post_comments', [
        'id' => $comment->id,
    ]);
});

test('user cannot delete another users comment', function () {
    $author = User::factory()->employee()->create();
    $owner = User::factory()->employee()->create();
    actingAsEmployee();
    $post = Post::factory()->for($author, 'author')->published()->create([
        'author_role' => $author->role,
    ]);
    $comment = PostComment::factory()->for($post)->for($owner, 'user')->create([
        'body' => 'Owned by someone else',
    ]);

    $this->delete(route('feed.posts.comments.destroy', [$post, $comment]))
        ->assertForbidden();

    $this->assertDatabaseHas('post_comments', [
        'id' => $comment->id,
    ]);
});

test('blocked user cannot comment on the feed', function () {
    $author = User::factory()->employee()->create();
    $blocked = User::factory()->employee()->create(['is_blocked_from_posts' => true]);
    $post = Post::factory()->for($author, 'author')->published()->create([
        'author_role' => $author->role,
    ]);

    $this->actingAs($blocked)
        ->post(route('feed.posts.comments.store', $post), [
            'body' => 'Should not comment',
        ])
        ->assertForbidden();
});

test('destroy rejects comments that do not belong to the post', function () {
    $viewer = actingAsEmployee();
    $postA = Post::factory()->for($viewer, 'author')->published()->create([
        'author_role' => $viewer->role,
    ]);
    $postB = Post::factory()->for($viewer, 'author')->published()->create([
        'author_role' => $viewer->role,
    ]);
    $comment = PostComment::factory()->for($postB)->for($viewer, 'user')->create([
        'body' => 'Belongs to post B',
    ]);

    $this->delete(route('feed.posts.comments.destroy', [$postA, $comment]))
        ->assertNotFound();
});
