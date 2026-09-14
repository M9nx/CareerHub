<?php

use App\Models\EmployeeProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('employee can update their profile', function () {
    $user = User::factory()->employee()->create();

    EmployeeProfile::factory()->create([
        'user_id' => $user->id,
        'cv_path' => 'old-cv.pdf',
        'application_image_path' => 'old-image.jpg',
    ]);

    $response = $this->actingAs($user)->patch(
        route('employee.profile.update'),
        [
            'cv_path' => 'new-cv.pdf',
            'application_image_path' => 'new-image.jpg',
        ]
    );

    $response
        ->assertRedirect(route('employee.profile.edit'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('employee_profiles', [
        'user_id' => $user->id,
        'cv_path' => 'new-cv.pdf',
        'application_image_path' => 'new-image.jpg',
    ]);
});
