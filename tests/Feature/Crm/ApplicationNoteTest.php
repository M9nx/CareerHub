<?php

use App\Filament\SuperAdmin\Resources\ApplicationResource\Pages\ListApplications;
use App\Filament\SuperAdmin\Resources\ApplicationResource\Pages\ViewApplication;
use App\Filament\SuperAdmin\Resources\ApplicationResource\RelationManagers\NotesRelationManager;
use App\Models\Application;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel('super-admin');
});

test('super admin can add a note to an application', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $application = Application::factory()->create();

    $this->actingAs($superAdmin, 'filament');

    Livewire::test(NotesRelationManager::class, [
        'ownerRecord' => $application,
        'pageClass' => ViewApplication::class,
    ])
        ->callAction(TestAction::make('create')->table(), [
            'body' => 'Follow up with the candidate.',
            'author_id' => User::factory()->employer()->create()->id,
        ])
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('application_notes', [
        'application_id' => $application->id,
        'author_id' => $superAdmin->id,
        'body' => 'Follow up with the candidate.',
    ]);
});

test('super admin can view an application and its notes', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $employee = User::factory()->employee()->create([
        'name' => 'Ada Applicant',
    ]);
    $application = Application::factory()->for($employee, 'employee')->create([
        'cover_letter' => 'I would like to join the team.',
    ]);
    $application->notes()->create([
        'author_id' => $superAdmin->id,
        'body' => 'Follow up with the candidate.',
    ]);

    $this->actingAs($superAdmin, 'filament');

    Livewire::test(ListApplications::class)
        ->assertSuccessful()
        ->assertSee('Ada Applicant');

    Livewire::test(ViewApplication::class, ['record' => $application->getRouteKey()])
        ->assertSuccessful()
        ->assertSee('Ada Applicant')
        ->assertSee('I would like to join the team.');

    Livewire::test(NotesRelationManager::class, [
        'ownerRecord' => $application,
        'pageClass' => ViewApplication::class,
    ])
        ->assertSuccessful()
        ->assertSee('Follow up with the candidate.')
        ->assertSee($superAdmin->name);
});

test('application notes require a body', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $application = Application::factory()->create();

    $this->actingAs($superAdmin, 'filament');

    Livewire::test(NotesRelationManager::class, [
        'ownerRecord' => $application,
        'pageClass' => ViewApplication::class,
    ])
        ->callAction(TestAction::make('create')->table(), [
            'body' => '',
        ])
        ->assertHasFormErrors(['body' => 'required']);

    $this->assertDatabaseMissing('application_notes', [
        'application_id' => $application->id,
    ]);
});

test('employer cannot access application notes', function () {
    $employer = User::factory()->employer()->create();
    $application = Application::factory()->create();

    $this->actingAs($employer, 'filament')
        ->get('/super-admin/applications')
        ->assertForbidden();

    Livewire::test(ListApplications::class)
        ->assertForbidden();

    Livewire::test(ViewApplication::class, ['record' => $application->getRouteKey()])
        ->assertForbidden();
});

test('application notes escape body content', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $application = Application::factory()->create();
    $application->notes()->create([
        'author_id' => $superAdmin->id,
        'body' => '<script>alert("xss")</script>',
    ]);

    $this->actingAs($superAdmin, 'filament');

    Livewire::test(NotesRelationManager::class, [
        'ownerRecord' => $application,
        'pageClass' => ViewApplication::class,
    ])
        ->assertSuccessful()
        ->assertDontSee('<script>alert("xss")</script>', false)
        ->assertSee('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', false);
});
