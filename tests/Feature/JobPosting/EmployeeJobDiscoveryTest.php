<?php

use App\Enums\EmploymentType;
use App\Models\JobPosting;
use App\Models\User;

test('employee job discovery shows split list and selected detail', function () {
    actingAsEmployee();
    $employer = User::factory()->employer()->create();
    $employer->employerProfile()->create(['company_name' => 'Discovery Co']);

    $job = JobPosting::factory()->for($employer, 'employer')->published()->create([
        'title' => 'Discovery Split Role',
        'description' => 'Detailed discovery description body.',
        'location' => 'Remote',
        'employment_type' => EmploymentType::FullTime,
    ]);

    $this->get(route('employee.jobs.index'))
        ->assertOk()
        ->assertSee('Discovery Split Role')
        ->assertSee('Detailed discovery description body.')
        ->assertSee('Remote')
        ->assertSee(EmploymentType::FullTime->label())
        ->assertSee(__('Open & apply'));

    $this->get(route('employee.jobs.index', ['selected' => $job->id]))
        ->assertOk()
        ->assertSee('Discovery Split Role');
});

test('employee can filter jobs by location and employment type', function () {
    actingAsEmployee();
    $employer = User::factory()->employer()->create();

    JobPosting::factory()->for($employer, 'employer')->published()->create([
        'title' => 'Cairo Full Time Role',
        'location' => 'Cairo',
        'employment_type' => EmploymentType::FullTime,
    ]);
    JobPosting::factory()->for($employer, 'employer')->published()->create([
        'title' => 'Alex Part Time Role',
        'location' => 'Alexandria',
        'employment_type' => EmploymentType::PartTime,
    ]);

    $this->get(route('employee.jobs.index', [
        'location' => 'Cairo',
        'employment_type' => EmploymentType::FullTime->value,
    ]))
        ->assertOk()
        ->assertSee('Cairo Full Time Role')
        ->assertDontSee('Alex Part Time Role');
});
