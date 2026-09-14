<?php

use App\Models\JobPosting;

test('employee search returns only published jobs matching the title', function () {
    actingAsEmployee();

    JobPosting::factory()->published()->create([
        'title' => 'Senior Laravel Developer',
    ]);

    JobPosting::factory()->published()->create([
        'title' => 'Frontend Designer',
    ]);

    JobPosting::factory()->draft()->create([
        'title' => 'Draft Laravel Intern',
    ]);

    $this->get(route('employee.jobs.index', ['search' => 'Laravel']))
        ->assertOk()
        ->assertSee('Senior Laravel Developer')
        ->assertDontSee('Frontend Designer')
        ->assertDontSee('Draft Laravel Intern')
        ->assertSee('value="Laravel"', false);
});

test('employee search treats like wildcards as literals', function () {
    actingAsEmployee();

    JobPosting::factory()->published()->create([
        'title' => '100% Remote Laravel Role',
    ]);

    JobPosting::factory()->published()->create([
        'title' => 'Onsite Laravel Role',
    ]);

    $this->get(route('employee.jobs.index', ['search' => '%']))
        ->assertOk()
        ->assertSee('100% Remote Laravel Role')
        ->assertDontSee('Onsite Laravel Role');
});

test('employee job search is preserved across pagination links', function () {
    actingAsEmployee();

    JobPosting::factory()
        ->published()
        ->count(11)
        ->sequence(fn ($sequence) => [
            'title' => sprintf('Laravel Role #%02d', $sequence->index + 1),
            'created_at' => now()->subMinutes(11 - $sequence->index),
        ])
        ->create();

    JobPosting::factory()->published()->create([
        'title' => 'Unrelated Designer Role',
    ]);

    $this->get(route('employee.jobs.index', ['search' => 'Laravel']))
        ->assertOk()
        ->assertSee('Laravel Role #11')
        ->assertDontSee('Unrelated Designer Role')
        ->assertSee('search=Laravel', false)
        ->assertSee('page=2', false);

    $this->get(route('employee.jobs.index', ['search' => 'Laravel', 'page' => 2]))
        ->assertOk()
        ->assertSee('Laravel Role #01')
        ->assertDontSee('Unrelated Designer Role')
        ->assertSee('value="Laravel"', false);
});
