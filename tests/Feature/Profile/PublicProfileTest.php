<?php

use App\Models\Post;
use App\Models\User;

test('authenticated peers can view an active public profile', function () {
    $viewer = actingAsEmployee();
    $person = User::factory()->employer()->create([
        'name' => 'Public Employer',
        'headline' => 'Hiring for platform teams',
        'location' => 'Riyadh',
        'about' => 'Building thoughtful hiring loops.',
        'is_active' => true,
    ]);
    $person->employerProfile()->create([
        'company_name' => 'Northwind Careers',
        'industry' => 'HR Tech',
    ]);

    Post::factory()->for($person, 'author')->published()->create([
        'author_role' => $person->role,
        'body' => 'Visible recent post body',
    ]);

    $this->get(route('people.show', $person))
        ->assertOk()
        ->assertSee('Public Employer')
        ->assertSee('Hiring for platform teams')
        ->assertSee('Riyadh')
        ->assertSee('Building thoughtful hiring loops.')
        ->assertSee('Northwind Careers')
        ->assertSee('HR Tech')
        ->assertSee('Visible recent post body')
        ->assertDontSee($person->email);
});

test('guests cannot view public profiles', function () {
    $person = User::factory()->employee()->create();

    $this->get(route('people.show', $person))
        ->assertRedirect(route('login'));
});

test('inactive profiles are not viewable to peers', function () {
    actingAsEmployee();
    $person = User::factory()->employee()->create(['is_active' => false]);

    $this->get(route('people.show', $person))
        ->assertForbidden();
});

test('profile owner sees edit profile link on their public page', function () {
    $user = actingAsEmployee(['name' => 'Self Viewer']);

    $this->get(route('people.show', $user))
        ->assertOk()
        ->assertSee(__('Edit profile'))
        ->assertSee(route('profile.edit'), false);
});
