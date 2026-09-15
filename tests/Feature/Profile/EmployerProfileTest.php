<?php

use App\Models\EmployerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('employer can update company name', function () {
    $user = User::factory()->employer()->create();

    EmployerProfile::factory()->for($user)->create([
        'company_name' => 'Old Company',
    ]);

    $this->actingAs($user)->patch(
        route('employer.profile.update'),
        [
            'company_name' => 'New Company',
        ]
    )
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHas('success', __('Profile updated successfully.'));

    $this->assertDatabaseHas('employer_profiles', [
        'user_id' => $user->id,
        'company_name' => 'New Company',
    ]);
});

test('employer company name appears on the account profile page', function () {
    $user = User::factory()->employer()->create();

    EmployerProfile::factory()->for($user)->create([
        'company_name' => 'Test Company',
    ]);

    $this->actingAs($user)
        ->get(route('profile.edit'))
        ->assertOk()
        ->assertSee('Company')
        ->assertSee('Company Name')
        ->assertSee('Test Company');
});

test('employer profile route redirects to the account profile page', function () {
    $user = User::factory()->employer()->create();

    $this->actingAs($user)
        ->get(route('employer.profile.edit'))
        ->assertRedirect(route('profile.edit'));
});

test('employer profile is created when missing', function () {
    $user = User::factory()->employer()->create();

    $this->assertDatabaseMissing('employer_profiles', [
        'user_id' => $user->id,
    ]);

    $this->actingAs($user)
        ->get(route('profile.edit'))
        ->assertOk();

    $this->assertDatabaseHas('employer_profiles', [
        'user_id' => $user->id,
    ]);
});

test('empty company name fails validation', function () {
    $user = User::factory()->employer()->create();

    EmployerProfile::factory()->for($user)->create([
        'company_name' => 'Existing Company',
    ]);

    $this->actingAs($user)->patch(
        route('employer.profile.update'),
        [
            'company_name' => '',
        ]
    )
        ->assertSessionHasErrors('company_name')
        ->assertInvalid(['company_name' => __('validation.required', ['attribute' => 'company name'])]);
});

test('company name longer than 255 characters fails validation', function () {
    $user = User::factory()->employer()->create();

    EmployerProfile::factory()->for($user)->create([
        'company_name' => 'Existing Company',
    ]);

    $this->actingAs($user)
        ->from(route('profile.edit'))
        ->patch(route('employer.profile.update'), [
            'company_name' => str_repeat('a', 256),
        ])
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHasErrors('company_name')
        ->assertInvalid(['company_name' => __('validation.max.string', ['attribute' => 'company name', 'max' => 255])]);
});

test('guest is redirected to login from employer profile', function () {
    $this->get(route('employer.profile.edit'))
        ->assertRedirect(route('login'));

    $this->patch(route('employer.profile.update'), [
        'company_name' => 'Guest Company',
    ])->assertRedirect(route('login'));
});

test('employee cannot view or update employer profile', function () {
    $user = User::factory()->employee()->create();

    $this->actingAs($user)
        ->get(route('employer.profile.edit'))
        ->assertForbidden();

    $this->actingAs($user)
        ->patch(route('employer.profile.update'), [
            'company_name' => 'Unauthorized Company',
        ])
        ->assertForbidden();
});

test('employee breeze profile does not show employer company fields', function () {
    $user = User::factory()->employee()->create();

    $this->actingAs($user)
        ->get(route('profile.edit'))
        ->assertOk()
        ->assertDontSee(__('Company details shown on your public profile and job postings.'));
});
