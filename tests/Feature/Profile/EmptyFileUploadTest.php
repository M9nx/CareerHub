<?php

use App\Models\EmployeeProfile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('professional profile accepts cover when avatar input is an empty upload', function () {
    Storage::fake('public');
    $user = actingAsEmployee();

    $emptyAvatar = new UploadedFile(
        tempnam(sys_get_temp_dir(), 'avatar'),
        '',
        'application/octet-stream',
        UPLOAD_ERR_NO_FILE,
        true,
    );

    $this->patch(route('profile.professional.update'), [
        'cover' => UploadedFile::fake()->image('cover.jpg', 800, 300),
        'avatar' => $emptyAvatar,
    ])
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHas('success')
        ->assertSessionDoesntHaveErrors();

    $user->refresh();

    expect($user->cover_path)->not->toBeNull()
        ->and($user->avatar_path)->toBeNull();

    Storage::disk('public')->assertExists($user->cover_path);
});

test('employee can upload only a cv when application image input is empty', function () {
    Storage::fake('public');

    $employee = actingAsEmployee();
    EmployeeProfile::factory()->for($employee)->create([
        'cv_path' => '',
        'application_image_path' => null,
    ]);

    $emptyImage = new UploadedFile(
        tempnam(sys_get_temp_dir(), 'appimg'),
        '',
        'application/octet-stream',
        UPLOAD_ERR_NO_FILE,
        true,
    );

    $this->patch(route('employee.profile.update'), [
        'cv' => UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf'),
        'application_image' => $emptyImage,
    ])
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHas('success')
        ->assertSessionDoesntHaveErrors();

    $profile = $employee->fresh()->employeeProfile;

    expect($profile->cv_path)->not->toBe('')
        ->and($profile->application_image_path)->toBeNull();

    Storage::disk('public')->assertExists($profile->cv_path);
});
