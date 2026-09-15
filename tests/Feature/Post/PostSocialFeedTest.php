<?php

use App\Models\Post;
use App\Models\PostAttachment;
use App\Models\PostReaction;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('employee can publish a post with attachments from the feed composer', function () {
    Storage::fake('public');
    $employee = actingAsEmployee();

    $this->post(route('feed.store'), [
        'body' => 'Sharing a portfolio PDF with the community.',
        'attachments' => [
            UploadedFile::fake()->create('portfolio.pdf', 100, 'application/pdf'),
            UploadedFile::fake()->image('screenshot.png'),
        ],
    ])->assertRedirect(route('feed.index'));

    $post = Post::query()->where('author_id', $employee->id)->firstOrFail();

    expect($post->body)->toBe('Sharing a portfolio PDF with the community.')
        ->and($post->attachments)->toHaveCount(2);

    $this->get(route('feed.index'))
        ->assertOk()
        ->assertSee('Sharing a portfolio PDF with the community.')
        ->assertSee('portfolio.pdf');
});

test('user can like and unlike a published post', function () {
    $author = User::factory()->employee()->create();
    $viewer = actingAsEmployee();
    $post = Post::factory()->for($author, 'author')->published()->create([
        'body' => 'Likeable feed post',
        'author_role' => $author->role,
    ]);

    $this->from(route('feed.index'))
        ->post(route('feed.posts.react', $post))
        ->assertRedirect(route('feed.index'));

    expect(PostReaction::query()->where('post_id', $post->id)->where('user_id', $viewer->id)->exists())->toBeTrue();

    $this->from(route('feed.index'))
        ->post(route('feed.posts.react', $post))
        ->assertRedirect(route('feed.index'));

    expect(PostReaction::query()->where('post_id', $post->id)->where('user_id', $viewer->id)->exists())->toBeFalse();
});

test('user can share a post to their feed with an optional comment', function () {
    $author = User::factory()->employer()->create(['name' => 'Original Author']);
    $sharer = actingAsEmployee(['name' => 'Sharing Employee']);
    $post = Post::factory()->for($author, 'author')->published()->create([
        'title' => 'Original Post Title',
        'body' => 'Original post everyone should see.',
        'author_role' => $author->role,
    ]);

    $this->post(route('feed.posts.share', $post), [
        'comment' => 'Great insight — resharing this.',
    ])->assertRedirect(route('feed.index'));

    $shared = Post::query()
        ->where('author_id', $sharer->id)
        ->where('shared_post_id', $post->id)
        ->firstOrFail();

    expect($shared->body)->toBe('Great insight — resharing this.');

    $this->get(route('feed.index'))
        ->assertOk()
        ->assertSee('Great insight — resharing this.')
        ->assertSee('Original post everyone should see.')
        ->assertSee('Sharing Employee');
});

test('blocked user cannot publish react or share on the feed', function () {
    $author = User::factory()->employee()->create();
    $blocked = User::factory()->employee()->create(['is_blocked_from_posts' => true]);
    $post = Post::factory()->for($author, 'author')->published()->create([
        'author_role' => $author->role,
    ]);

    $this->actingAs($blocked)
        ->post(route('feed.store'), ['body' => 'Should not publish'])
        ->assertForbidden();

    $this->actingAs($blocked)
        ->post(route('feed.posts.react', $post))
        ->assertForbidden();

    $this->actingAs($blocked)
        ->post(route('feed.posts.share', $post), ['comment' => 'Nope'])
        ->assertForbidden();
});

test('post attachments are removed when the post is deleted', function () {
    Storage::fake('public');
    $employee = actingAsEmployee();
    $post = Post::factory()->for($employee, 'author')->published()->create([
        'author_role' => $employee->role,
    ]);

    PostAttachment::create([
        'post_id' => $post->id,
        'path' => 'post-attachments/test.pdf',
        'original_name' => 'test.pdf',
        'mime_type' => 'application/pdf',
        'size' => 100,
    ]);

    Storage::disk('public')->put('post-attachments/test.pdf', 'pdf-content');

    $post->delete();

    expect(PostAttachment::query()->where('post_id', $post->id)->exists())->toBeFalse();
});
