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
        ->assertSessionHas('success');

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

    $response->assertSessionHasErrors('company_name');
});

test('employee cannot update employer profile', function () {
    $user = User::factory()->employee()->create();

    $response = $this->actingAs($user)->patch(
        route('employer.profile.update'),
        [
            'company_name' => 'Unauthorized Company',
        ]
    );

    $response->assertForbidden();
});
