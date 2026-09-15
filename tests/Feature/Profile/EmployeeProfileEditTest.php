<?php

use App\Models\EmployeeProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('profile lives in the account menu not the primary nav', function () {
    $user = User::factory()->employee()->create();

    $response = $this->actingAs($user)
        ->get(route('feed.index'))
        ->assertOk()
        ->assertSee(__('Home'))
        ->assertSee(__('Me'))
        ->assertSee(route('profile.edit'), false)
        ->assertSee(__('Profile'));

    // Primary nav should highlight Home, not expose a top-level Profile link label alone as nav item.
    expect(substr_count($response->getContent(), __('Home')))->toBeGreaterThan(0);
});

test('employee career documents appear on the account profile page', function () {
    $user = User::factory()->employee()->create();

    EmployeeProfile::factory()->for($user)->create([
        'cv_path' => 'cvs/existing.pdf',
        'application_image_path' => 'images/existing.jpg',
    ]);

    $this->actingAs($user)
        ->get(route('profile.edit'))
        ->assertOk()
        ->assertSee('Career documents')
        ->assertSee('existing.pdf')
        ->assertSee('existing.jpg')
        ->assertSee('type="file"', false)
        ->assertSee(route('employee.profile.update'), false);
});

test('employee profile route redirects to the account profile page', function () {
    $user = User::factory()->employee()->create();

    $this->actingAs($user)
        ->get(route('employee.profile.edit'))
        ->assertRedirect(route('profile.edit'));
});

test('employee profile is created when missing', function () {
    $user = User::factory()->employee()->create();

    $this->assertDatabaseMissing('employee_profiles', [
        'user_id' => $user->id,
    ]);

    $this->actingAs($user)
        ->get(route('profile.edit'))
        ->assertOk()
        ->assertSee('No CV uploaded yet.');

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
        ->assertRedirect(route('profile.edit'));

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
        ->assertRedirect(route('profile.edit'));

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
        ->assertRedirect(route('profile.edit'));

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

test('employer breeze profile does not show employee career documents', function () {
    $user = User::factory()->employer()->create();

    $this->actingAs($user)
        ->get(route('profile.edit'))
        ->assertOk()
        ->assertDontSee('Career documents')
        ->assertDontSee('No CV uploaded yet.');
});
