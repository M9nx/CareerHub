<?php

use App\Models\JobPosting;
use App\Models\Post;
use App\Models\User;

test('feed shows three column network shell landmarks', function () {
    $employee = actingAsEmployee(['name' => 'Shell Employee']);

    $this->get(route('feed.index'))
        ->assertOk()
        ->assertSee(__('Home'))
        ->assertSee(__('Network'))
        ->assertSee('Shell Employee')
        ->assertSee(__('Profile settings'))
        ->assertSee(__('Profile strength'))
        ->assertSee(__('Recent jobs'))
        ->assertSee(__('People posting'))
        ->assertSee(__('Start a post'))
        ->assertDontSee('profile views', false)
        ->assertDontSee('impressions', false);
});

test('feed sidebar lists published jobs and other authors from real data', function () {
    $viewer = actingAsEmployee(['name' => 'Viewer Person']);
    $author = User::factory()->employer()->create(['name' => 'Visible Author']);
    $employer = User::factory()->employer()->create(['name' => 'Hiring Lead']);

    Post::factory()->for($author, 'author')->published()->create([
        'author_role' => $author->role,
        'body' => 'Author body for suggestions.',
    ]);

    JobPosting::factory()->for($employer, 'employer')->published()->create([
        'title' => 'Suggested Sidebar Role',
    ]);

    $this->get(route('feed.index'))
        ->assertOk()
        ->assertSee('Suggested Sidebar Role')
        ->assertSee('Visible Author')
        ->assertSee('Viewer Person');
});
