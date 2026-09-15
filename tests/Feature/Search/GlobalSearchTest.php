<?php

use App\Models\EmployerProfile;
use App\Models\JobPosting;
use App\Models\Post;
use App\Models\User;

test('global search finds people jobs companies and posts', function () {
    $viewer = actingAsEmployee();

    $person = User::factory()->employer()->create([
        'name' => 'UniqueSearch Person',
        'headline' => 'Platform lead',
    ]);
    EmployerProfile::factory()->for($person)->create([
        'company_name' => 'UniqueSearch Labs',
        'industry' => 'Software',
    ]);
    $job = JobPosting::factory()->for($person, 'employer')->published()->create([
        'title' => 'UniqueSearch Engineer',
        'location' => 'Cairo',
    ]);
    Post::factory()->for($person, 'author')->published()->create([
        'author_role' => $person->role,
        'body' => 'Talking about UniqueSearch topics today.',
    ]);

    $this->get(route('search.index', ['q' => 'UniqueSearch']))
        ->assertOk()
        ->assertSee('UniqueSearch Person')
        ->assertSee('UniqueSearch Engineer')
        ->assertSee('UniqueSearch Labs')
        ->assertSee('Talking about UniqueSearch topics today.');
});

test('global search can filter to a single type', function () {
    actingAsEmployee();
    $person = User::factory()->employer()->create(['name' => 'Typed Search User']);
    EmployerProfile::factory()->for($person)->create(['company_name' => 'Typed Co']);
    JobPosting::factory()->for($person, 'employer')->published()->create([
        'title' => 'Typed Search Role',
    ]);

    $this->get(route('search.index', ['q' => 'Typed Search', 'type' => 'jobs']))
        ->assertOk()
        ->assertSee('Typed Search Role')
        ->assertDontSee('Typed Search User');
});

test('empty query shows prompt without inventing results', function () {
    actingAsEmployee();

    $this->get(route('search.index'))
        ->assertOk()
        ->assertSee(__('Enter a keyword to search CareerHub.'));
});

test('guests cannot use global search', function () {
    $this->get(route('search.index', ['q' => 'anything']))
        ->assertRedirect(route('login'));
});

test('header search form posts to the search route', function () {
    actingAsEmployee();

    $this->get(route('feed.index'))
        ->assertOk()
        ->assertSee(route('search.index'), false)
        ->assertSee('name="q"', false);
});
