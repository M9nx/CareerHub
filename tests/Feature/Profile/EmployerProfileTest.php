<?php

use App\Models\EmployerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('employer can update company name', function () {
    $user = User::factory()->employer()->create();

    EmployerProfile::factory()->create([
        'user_id' => $user->id,
        'company_name' => 'Old Company',
    ]);

    $response = $this->actingAs($user)->patch(
        route('employer.profile.update'),
        [
            'company_name' => 'New Company',
        ]
    );

    $response
        ->assertRedirect(route('employer.profile.edit'))
        ->assertSessionHas('success', __('Profile updated successfully.'));

    $this->assertDatabaseHas('employer_profiles', [
        'user_id' => $user->id,
        'company_name' => 'New Company',
    ]);
});

test('employer can view profile edit page', function () {
    $user = User::factory()->employer()->create();

    EmployerProfile::factory()->create([
        'user_id' => $user->id,
        'company_name' => 'Test Company',
    ]);

    $response = $this->actingAs($user)->get(
        route('employer.profile.edit')
    );

    $response
        ->assertOk()
        ->assertSee('Edit Employer Profile')
        ->assertSee('Company Name')
        ->assertSee('Test Company');
});

test('employer profile is created when missing', function () {
    $user = User::factory()->employer()->create();

    $this->assertDatabaseMissing('employer_profiles', [
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)->get(
        route('employer.profile.edit')
    );

    $response->assertOk();

    $this->assertDatabaseHas('employer_profiles', [
        'user_id' => $user->id,
    ]);
});

test('empty company name fails validation', function () {
    $user = User::factory()->employer()->create();

    EmployerProfile::factory()->create([
        'user_id' => $user->id,
        'company_name' => 'Existing Company',
    ]);

    $response = $this->actingAs($user)->patch(
        route('employer.profile.update'),
        [
            'company_name' => '',
        ]
    );

    $response
        ->assertSessionHasErrors('company_name')
        ->assertInvalid(['company_name' => __('validation.required', ['attribute' => 'company name'])]);
});

test('company name longer than 255 characters fails validation', function () {
    $user = User::factory()->employer()->create();

    EmployerProfile::factory()->create([
        'user_id' => $user->id,
        'company_name' => 'Existing Company',
    ]);

    $this->actingAs($user)
        ->from(route('employer.profile.edit'))
        ->patch(route('employer.profile.update'), [
            'company_name' => str_repeat('a', 256),
        ])
        ->assertRedirect(route('employer.profile.edit'))
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
