<?php

use App\Models\EmployeeProfile;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

test('employee can download their own cv', function () {
    Storage::fake('public');

    $employee = actingAsEmployee([
        'name' => 'Ada Lovelace',
    ]);

    Storage::disk('public')->put('cvs/ada-resume.pdf', '%PDF-1.4 fake-cv');

    EmployeeProfile::factory()->for($employee)->create([
        'cv_path' => 'cvs/ada-resume.pdf',
    ]);

    $this->get(route('employee.profile.cv.download'))
        ->assertOk()
        ->assertDownload('ada-lovelace-cv.pdf');
});

test('employee can download their application image', function () {
    Storage::fake('public');

    $employee = actingAsEmployee([
        'name' => 'Ada Lovelace',
    ]);

    Storage::disk('public')->put('application-images/portrait.jpg', 'fake-image');

    EmployeeProfile::factory()->for($employee)->create([
        'cv_path' => '',
        'application_image_path' => 'application-images/portrait.jpg',
    ]);

    $this->get(route('employee.profile.application-image.download'))
        ->assertOk()
        ->assertDownload('ada-lovelace-application-image.jpg');
});

test('cv download returns 404 when no cv is uploaded', function () {
    Storage::fake('public');

    $employee = actingAsEmployee();

    EmployeeProfile::factory()->for($employee)->create([
        'cv_path' => '',
        'application_image_path' => null,
    ]);

    $this->get(route('employee.profile.cv.download'))
        ->assertNotFound();
});

test('cv download returns 404 when stored file is missing from disk', function () {
    Storage::fake('public');

    $employee = actingAsEmployee();

    EmployeeProfile::factory()->for($employee)->create([
        'cv_path' => 'cvs/missing.pdf',
    ]);

    $this->get(route('employee.profile.cv.download'))
        ->assertNotFound();
});

test('guest is redirected to login from cv download', function () {
    $this->get(route('employee.profile.cv.download'))
        ->assertRedirect(route('login'));
});

test('employer cannot download employee cv', function () {
    Storage::fake('public');

    $employer = User::factory()->employer()->create();

    $this->actingAs($employer)
        ->get(route('employee.profile.cv.download'))
        ->assertForbidden();
});

test('profile page links cv to the authorized download route', function () {
    Storage::fake('public');

    $employee = actingAsEmployee();

    Storage::disk('public')->put('cvs/current.pdf', 'cv');

    EmployeeProfile::factory()->for($employee)->create([
        'cv_path' => 'cvs/current.pdf',
    ]);

    $this->get(route('profile.edit'))
        ->assertOk()
        ->assertSee(route('employee.profile.cv.download'), false)
        ->assertSee(__('Download'));
});
