<?php

use App\Models\EmployeeProfile;
use App\Models\EmployerProfile;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('employee uploads and downloads a cv while employer company name appears on job browse', function () {
    Storage::fake('public');

    $employer = User::factory()->employer()->create(['name' => 'Storage Employer']);
    EmployerProfile::factory()->for($employer)->create([
        'company_name' => 'Orbit Labs',
    ]);

    $job = JobPosting::factory()->for($employer, 'employer')->published()->create([
        'title' => 'Storage Integration Engineer',
    ]);

    $employee = actingAsEmployee(['name' => 'Storage Employee']);
    EmployeeProfile::factory()->for($employee)->create([
        'cv_path' => '',
        'application_image_path' => null,
    ]);

    $cv = UploadedFile::fake()->create('integration-resume.pdf', 200, 'application/pdf');

    $this->from(route('profile.edit'))
        ->patch(route('employee.profile.update'), [
            'cv' => $cv,
        ])
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHas('success', __('Career documents updated successfully.'));

    $profile = $employee->fresh()->employeeProfile;

    expect($profile->cv_path)->not->toBe('');
    Storage::disk('public')->assertExists($profile->cv_path);

    $this->get(route('employee.profile.cv.download'))
        ->assertOk()
        ->assertDownload('storage-employee-cv.pdf');

    $this->get(route('employee.jobs.index'))
        ->assertOk()
        ->assertSee('Storage Integration Engineer')
        ->assertSee('Orbit Labs');

    $this->get(route('employee.jobs.show', $job))
        ->assertOk()
        ->assertSee('Storage Integration Engineer')
        ->assertSee('Orbit Labs');
});
