<?php

use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Models\Post;
use App\Models\User;

test('employer can create a published post', function () {
    $employer = actingAsEmployer();

    $this->post(route('employer.posts.store'), [
        'title' => 'Employer Career Post',
        'body' => 'This is an employer career post.',
        'publish' => '1',
    ])
        ->assertRedirect(route('employer.posts.index'))
        ->assertSessionHas('success', __('Post created successfully.'));

    $this->assertDatabaseHas('posts', [
        'author_id' => $employer->id,
        'author_role' => UserRole::Employer->value,
        'title' => 'Employer Career Post',
        'body' => 'This is an employer career post.',
        'status' => PostStatus::Published->value,
        'is_active' => true,
    ]);
});

test('published employer post is visible on the shared feed', function () {
    $employer = actingAsEmployer();

    Post::factory()->for($employer, 'author')->published()->create([
        'author_role' => UserRole::Employer,
        'title' => 'Employer Feed Post',
    ]);

    $this->get(route('feed.index'))
        ->assertOk()
        ->assertSee('Employer Feed Post');
});

test('employer can update own post', function () {
    $employer = actingAsEmployer();
    $post = Post::factory()->for($employer, 'author')->create([
        'author_role' => UserRole::Employer,
        'title' => 'Old Employer Post',
    ]);

    $this->put(route('employer.posts.update', $post), [
        'title' => 'Updated Employer Post',
        'body' => 'Updated employer post body.',
        'publish' => '1',
    ])->assertRedirect(route('employer.posts.index'));

    $this->assertDatabaseHas('posts', [
        'id' => $post->id,
        'author_id' => $employer->id,
        'title' => 'Updated Employer Post',
        'body' => 'Updated employer post body.',
        'status' => PostStatus::Published->value,
    ]);
});

test('employer can delete own post', function () {
    $employer = actingAsEmployer();
    $post = Post::factory()->for($employer, 'author')->create([
        'author_role' => UserRole::Employer,
    ]);

    $this->delete(route('employer.posts.destroy', $post))
        ->assertRedirect(route('employer.posts.index'));

    $this->assertDatabaseMissing('posts', [
        'id' => $post->id,
    ]);
});

test('employer cannot update another users post', function () {
    actingAsEmployer();
    $otherEmployer = User::factory()->employer()->create();
    $post = Post::factory()->for($otherEmployer, 'author')->create([
        'author_role' => UserRole::Employer,
    ]);

    $this->put(route('employer.posts.update', $post), [
        'title' => 'Unauthorized Update',
        'body' => 'This update should not be allowed.',
        'publish' => '1',
    ])->assertForbidden();

    $this->assertDatabaseMissing('posts', [
        'id' => $post->id,
        'title' => 'Unauthorized Update',
    ]);
});

test('employer post show route is not registered', function () {
    $employer = actingAsEmployer();
    $post = Post::factory()->for($employer, 'author')->create([
        'author_role' => UserRole::Employer,
    ]);

    $this->get('/employer/posts/'.$post->id)->assertMethodNotAllowed();
});

test('employer posts index lists own posts', function () {
    $employer = actingAsEmployer();
    Post::factory()->for($employer, 'author')->create([
        'author_role' => UserRole::Employer,
        'title' => 'Own Employer Post',
    ]);

    $this->get(route('employer.posts.index'))
        ->assertOk()
        ->assertSee('Own Employer Post')
        ->assertSee(route('employer.posts.create'), false);
});
