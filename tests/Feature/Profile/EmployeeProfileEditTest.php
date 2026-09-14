<?php

use App\Models\EmployeeProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('employee can view profile edit page', function () {
    $user = User::factory()->employee()->create();

    EmployeeProfile::factory()->for($user)->create([
        'cv_path' => 'cvs/existing.pdf',
        'application_image_path' => 'images/existing.jpg',
    ]);

    $this->actingAs($user)
        ->get(route('employee.profile.edit'))
        ->assertOk()
        ->assertSee('Edit Employee Profile')
        ->assertSee('CV uploaded')
        ->assertSee('Application image uploaded')
        ->assertDontSee('type="file"', false);
});

test('employee profile is created when missing', function () {
    $user = User::factory()->employee()->create();

    $this->assertDatabaseMissing('employee_profiles', [
        'user_id' => $user->id,
    ]);

    $this->actingAs($user)
        ->get(route('employee.profile.edit'))
        ->assertOk()
        ->assertSee('CV upload will be available in a later phase.');

    $this->assertDatabaseHas('employee_profiles', [
        'user_id' => $user->id,
        'cv_path' => '',
        'application_image_path' => null,
    ]);
});

test('employee update creates profile when missing', function () {
    $user = User::factory()->employee()->create();

    $this->actingAs($user)
        ->patch(route('employee.profile.update'))
        ->assertRedirect(route('employee.profile.edit'))
        ->assertSessionHas('success', __('Profile updated successfully.'));

    $this->assertDatabaseHas('employee_profiles', [
        'user_id' => $user->id,
        'cv_path' => '',
    ]);
});

test('employee can update their profile record', function () {
    $user = User::factory()->employee()->create();

    EmployeeProfile::factory()->for($user)->create([
        'cv_path' => 'cvs/existing.pdf',
        'application_image_path' => 'images/existing.jpg',
    ]);

    $this->actingAs($user)
        ->patch(route('employee.profile.update'))
        ->assertRedirect(route('employee.profile.edit'))
        ->assertSessionHas('success', __('Profile updated successfully.'));

    $this->assertDatabaseHas('employee_profiles', [
        'user_id' => $user->id,
        'cv_path' => 'cvs/existing.pdf',
        'application_image_path' => 'images/existing.jpg',
    ]);
});

test('employee profile update ignores client supplied file paths', function () {
    $user = User::factory()->employee()->create();

    EmployeeProfile::factory()->for($user)->create([
        'cv_path' => 'cvs/existing.pdf',
        'application_image_path' => 'images/existing.jpg',
    ]);

    $this->actingAs($user)
        ->patch(route('employee.profile.update'), [
            'cv_path' => '../secrets.pdf',
            'application_image_path' => 'https://evil.example/x.jpg',
        ])
        ->assertRedirect(route('employee.profile.edit'));

    $this->assertDatabaseHas('employee_profiles', [
        'user_id' => $user->id,
        'cv_path' => 'cvs/existing.pdf',
        'application_image_path' => 'images/existing.jpg',
    ]);
});

test('guest is redirected to login from employee profile', function () {
    $this->get(route('employee.profile.edit'))
        ->assertRedirect(route('login'));

    $this->patch(route('employee.profile.update'))
        ->assertRedirect(route('login'));
});

test('employer cannot view or update employee profile', function () {
    $user = User::factory()->employer()->create();

    $this->actingAs($user)
        ->get(route('employee.profile.edit'))
        ->assertForbidden();

    $this->actingAs($user)
        ->patch(route('employee.profile.update'))
        ->assertForbidden();
});
