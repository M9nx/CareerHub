<?php

use App\Models\EmployeeProfile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('employee can upload a cv and application image', function () {
    Storage::fake('public');

    $employee = actingAsEmployee();
    EmployeeProfile::factory()->for($employee)->create([
        'cv_path' => '',
        'application_image_path' => null,
    ]);

    $cv = UploadedFile::fake()->create('resume.pdf', 200, 'application/pdf');
    $image = UploadedFile::fake()->image('portrait.jpg', 200, 200);

    $this->from(route('profile.edit'))
        ->patch(route('employee.profile.update'), [
            'cv' => $cv,
            'application_image' => $image,
        ])
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHas('success', __('Career documents updated successfully.'));

    $profile = $employee->fresh()->employeeProfile;

    expect($profile->cv_path)->not->toBe('')
        ->and($profile->application_image_path)->not->toBeNull();

    Storage::disk('public')->assertExists($profile->cv_path);
    Storage::disk('public')->assertExists($profile->application_image_path);
});

test('employee can replace an existing cv and old file is removed', function () {
    Storage::fake('public');

    $employee = actingAsEmployee();
    $oldPath = 'cvs/old-resume.pdf';
    Storage::disk('public')->put($oldPath, 'old-cv-content');

    EmployeeProfile::factory()->for($employee)->create([
        'cv_path' => $oldPath,
        'application_image_path' => null,
    ]);

    $cv = UploadedFile::fake()->create('new-resume.pdf', 120, 'application/pdf');

    $this->patch(route('employee.profile.update'), [
        'cv' => $cv,
    ])->assertRedirect(route('profile.edit'));

    $profile = $employee->fresh()->employeeProfile;

    expect($profile->cv_path)->not->toBe($oldPath);
    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($profile->cv_path);
});

test('employee profile page shows upload inputs and current files', function () {
    Storage::fake('public');

    $employee = actingAsEmployee();
    Storage::disk('public')->put('cvs/current.pdf', 'cv');
    Storage::disk('public')->put('application-images/current.jpg', 'img');

    EmployeeProfile::factory()->for($employee)->create([
        'cv_path' => 'cvs/current.pdf',
        'application_image_path' => 'application-images/current.jpg',
    ]);

    $this->get(route('profile.edit'))
        ->assertOk()
        ->assertSee('Career documents')
        ->assertSee('type="file"', false)
        ->assertSee('name="cv"', false)
        ->assertSee('name="application_image"', false)
        ->assertSee('current.pdf')
        ->assertSee('current.jpg')
        ->assertDontSee('CV upload will be available in a later phase.');
});

test('cv must be a pdf and application image must be jpg or png', function () {
    Storage::fake('public');

    $employee = actingAsEmployee();
    EmployeeProfile::factory()->for($employee)->create([
        'cv_path' => '',
        'application_image_path' => null,
    ]);

    $this->from(route('profile.edit'))
        ->patch(route('employee.profile.update'), [
            'cv' => UploadedFile::fake()->image('resume.jpg'),
            'application_image' => UploadedFile::fake()->create('photo.gif', 100, 'image/gif'),
        ])
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHasErrors(['cv', 'application_image']);

    $profile = $employee->fresh()->employeeProfile;

    expect($profile->cv_path)->toBe('')
        ->and($profile->application_image_path)->toBeNull();
});

test('client supplied path fields are ignored during upload', function () {
    Storage::fake('public');

    $employee = actingAsEmployee();
    EmployeeProfile::factory()->for($employee)->create([
        'cv_path' => 'cvs/existing.pdf',
        'application_image_path' => 'application-images/existing.jpg',
    ]);

    $this->patch(route('employee.profile.update'), [
        'cv_path' => '../secrets.pdf',
        'application_image_path' => 'https://evil.example/x.jpg',
        'cv' => UploadedFile::fake()->create('safe.pdf', 100, 'application/pdf'),
    ])->assertRedirect(route('profile.edit'));

    $profile = $employee->fresh()->employeeProfile;

    expect($profile->cv_path)->not->toBe('../secrets.pdf')
        ->and($profile->application_image_path)->toBe('application-images/existing.jpg');

    Storage::disk('public')->assertExists($profile->cv_path);
});

test('employer cannot upload employee career documents', function () {
    Storage::fake('public');

    $employer = User::factory()->employer()->create();

    $this->actingAs($employer)
        ->patch(route('employee.profile.update'), [
            'cv' => UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf'),
        ])
        ->assertForbidden();
});
