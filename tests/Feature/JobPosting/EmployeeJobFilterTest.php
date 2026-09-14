<?php

use App\Models\JobPosting;
use App\Models\User;

it('returns only published jobs matching the title search', function () {
    $employee = User::factory()->employee()->create();

    $matchingJob = JobPosting::factory()->published()->create([
        'title' => 'Senior Laravel Developer',
    ]);

    JobPosting::factory()->published()->create([
        'title' => 'Frontend Designer',
    ]);

    $response = $this
        ->actingAs($employee)
        ->get(route('employee.jobs.index', ['search' => 'Laravel']));

    $response
        ->assertOk()
        ->assertSee($matchingJob->title)
        ->assertDontSee('Frontend Designer');
});
