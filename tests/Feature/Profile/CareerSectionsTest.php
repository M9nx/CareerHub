<?php

use App\Models\ProfileEducation;
use App\Models\ProfileExperience;
use App\Models\ProfileSkill;
use App\Models\User;

test('user can add experience education and skills', function () {
    $user = actingAsEmployee();

    $this->post(route('profile.experiences.store'), [
        'title' => 'Software Engineer',
        'company' => 'CareerHub Labs',
        'location' => 'Cairo',
        'started_at' => '2022-01-01',
        'ended_at' => null,
        'description' => 'Built the feed.',
    ])
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHas('success');

    $this->post(route('profile.educations.store'), [
        'school' => 'Cairo University',
        'degree' => 'BSc',
        'field' => 'Computer Science',
        'started_at' => '2016-09-01',
        'ended_at' => '2020-06-01',
    ])
        ->assertRedirect(route('profile.edit'));

    $this->post(route('profile.skills.store'), [
        'name' => 'Laravel',
    ])
        ->assertRedirect(route('profile.edit'));

    expect($user->profileExperiences()->count())->toBe(1)
        ->and($user->profileEducations()->count())->toBe(1)
        ->and($user->profileSkills()->count())->toBe(1);

    $this->get(route('people.show', $user))
        ->assertOk()
        ->assertSee('Software Engineer')
        ->assertSee('CareerHub Labs')
        ->assertSee('Cairo University')
        ->assertSee('Laravel');
});

test('experience validation requires title company and start date', function () {
    actingAsEmployee();

    $this->from(route('profile.edit'))
        ->post(route('profile.experiences.store'), [])
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHasErrors(['title', 'company', 'started_at']);
});

test('user cannot update another users experience', function () {
    actingAsEmployee();
    $other = User::factory()->employee()->create();
    $experience = ProfileExperience::factory()->for($other)->create();

    $this->patch(route('profile.experiences.update', $experience), [
        'title' => 'Hacked',
        'company' => 'Nope',
        'started_at' => '2020-01-01',
    ])->assertForbidden();
});

test('user can remove their own career entries', function () {
    $user = actingAsEmployee();
    $experience = ProfileExperience::factory()->for($user)->create();
    $education = ProfileEducation::factory()->for($user)->create();
    $skill = ProfileSkill::factory()->for($user)->create(['name' => 'PHP']);

    $this->delete(route('profile.experiences.destroy', $experience))
        ->assertRedirect(route('profile.edit'));
    $this->delete(route('profile.educations.destroy', $education))
        ->assertRedirect(route('profile.edit'));
    $this->delete(route('profile.skills.destroy', $skill))
        ->assertRedirect(route('profile.edit'));

    expect(ProfileExperience::query()->whereKey($experience)->exists())->toBeFalse()
        ->and(ProfileEducation::query()->whereKey($education)->exists())->toBeFalse()
        ->and(ProfileSkill::query()->whereKey($skill)->exists())->toBeFalse();
});

test('public profile omits empty career sections', function () {
    $viewer = actingAsEmployee();
    $person = User::factory()->employee()->create(['name' => 'Sparse Profile']);

    $this->get(route('people.show', $person))
        ->assertOk()
        ->assertSee('Sparse Profile')
        ->assertDontSee(__('Experience'))
        ->assertDontSee(__('Education'))
        ->assertDontSee(__('Skills'));
});
