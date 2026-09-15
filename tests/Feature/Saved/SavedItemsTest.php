<?php

use App\Models\JobPosting;
use App\Models\Post;
use App\Models\SavedJob;
use App\Models\SavedPost;
use App\Models\User;

test('user can save and unsave a published job', function () {
    actingAsEmployee();
    $employer = User::factory()->employer()->create();
    $job = JobPosting::factory()->for($employer, 'employer')->published()->create([
        'title' => 'Saveable Role',
    ]);

    $this->post(route('saved.jobs.store', $job))
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('saved_jobs', [
        'job_posting_id' => $job->id,
    ]);

    $this->get(route('saved.index'))
        ->assertOk()
        ->assertSee('Saveable Role');

    $this->delete(route('saved.jobs.destroy', $job))
        ->assertRedirect();

    expect(SavedJob::query()->where('job_posting_id', $job->id)->exists())->toBeFalse();
});

test('user can save and unsave a published post', function () {
    $viewer = actingAsEmployee();
    $author = User::factory()->employer()->create();
    $post = Post::factory()->for($author, 'author')->published()->create([
        'author_role' => $author->role,
        'body' => 'Saveable feed post body',
    ]);

    $this->post(route('saved.posts.store', $post))
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('saved_posts', [
        'user_id' => $viewer->id,
        'post_id' => $post->id,
    ]);

    $this->delete(route('saved.posts.destroy', $post))
        ->assertRedirect();

    expect(SavedPost::query()->where('post_id', $post->id)->exists())->toBeFalse();
});

test('guests cannot access saved items', function () {
    $this->get(route('saved.index'))
        ->assertRedirect(route('login'));
});
