<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('user can update professional identity fields', function () {
    Storage::fake('public');
    $user = actingAsEmployee(['name' => 'Identity Employee']);

    $this->patch(route('profile.professional.update'), [
        'headline' => 'Backend engineer seeking roles',
        'location' => 'Cairo, Egypt',
        'about' => 'Building career tools.',
        'avatar' => UploadedFile::fake()->image('avatar.jpg'),
    ])
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHas('success');

    $user->refresh();

    expect($user->headline)->toBe('Backend engineer seeking roles')
        ->and($user->location)->toBe('Cairo, Egypt')
        ->and($user->about)->toBe('Building career tools.')
        ->and($user->avatar_path)->not->toBeNull();

    Storage::disk('public')->assertExists($user->avatar_path);
});

test('professional identity validation rejects oversized about text', function () {
    actingAsEmployee();

    $this->from(route('profile.edit'))
        ->patch(route('profile.professional.update'), [
            'about' => str_repeat('a', 5001),
        ])
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHasErrors('about');
});

test('employer can update enriched company fields', function () {
    Storage::fake('public');
    $user = actingAsEmployer();
    $user->employerProfile()->create(['company_name' => 'Old Co']);

    $this->patch(route('employer.profile.update'), [
        'company_name' => 'CareerHub Labs',
        'industry' => 'Software',
        'company_size' => '11-50',
        'location' => 'Remote',
        'website' => 'https://example.com',
        'about' => 'We hire carefully.',
        'logo' => UploadedFile::fake()->image('logo.png'),
    ])
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHas('success');

    $profile = $user->fresh()->employerProfile;

    expect($profile->company_name)->toBe('CareerHub Labs')
        ->and($profile->industry)->toBe('Software')
        ->and($profile->company_size)->toBe('11-50')
        ->and($profile->location)->toBe('Remote')
        ->and($profile->website)->toBe('https://example.com')
        ->and($profile->about)->toBe('We hire carefully.')
        ->and($profile->logo_path)->not->toBeNull();
});

test('feed profile summary shows headline and public profile link', function () {
    $user = actingAsEmployee([
        'name' => 'Summary Person',
        'headline' => 'Product-minded engineer',
        'location' => 'Alexandria',
    ]);

    $this->get(route('feed.index'))
        ->assertOk()
        ->assertSee('Summary Person')
        ->assertSee('Product-minded engineer')
        ->assertSee('Alexandria')
        ->assertSee(__('View public profile'))
        ->assertSee(route('people.show', $user), false);
});
