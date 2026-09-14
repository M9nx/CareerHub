<?php

use App\Enums\UserRole;
use App\Filament\SuperAdmin\Resources\UserResource\Pages\CreateUser;
use App\Filament\SuperAdmin\Resources\UserResource\Pages\EditUser;
use App\Filament\SuperAdmin\Resources\UserResource\Pages\ListUsers;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel('super-admin');
});

test('super admin can list users', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $otherUsers = User::factory()->count(3)->create();

    $this->actingAs($superAdmin, 'filament');

    Livewire::test(ListUsers::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords($otherUsers);
});

test('super admin can change a users role from the list', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $target = User::factory()->employer()->create();

    $this->actingAs($superAdmin, 'filament');

    Livewire::test(ListUsers::class)
        ->callAction(TestAction::make('changeRole')->table($target), [
            'role' => UserRole::Employee->value,
        ])
        ->assertHasNoFormErrors();

    expect($target->fresh()->role)->toBe(UserRole::Employee);
});

test('super admin can deactivate and activate a user from the list', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $target = User::factory()->employee()->create(['is_active' => true]);

    $this->actingAs($superAdmin, 'filament');

    Livewire::test(ListUsers::class)
        ->callAction(TestAction::make('deactivate')->table($target));

    expect($target->fresh()->is_active)->toBeFalse();

    Livewire::test(ListUsers::class)
        ->callAction(TestAction::make('activate')->table($target));

    expect($target->fresh()->is_active)->toBeTrue();
});

test('super admin can create an employer from the panel', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin, 'filament');

    Livewire::test(CreateUser::class)
        ->fillForm([
            'name' => 'New Employer',
            'email' => 'new-employer@careerhub.test',
            'password' => 'password',
            'role' => UserRole::Employer->value,
            'is_active' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified();

    $this->assertDatabaseHas('users', [
        'email' => 'new-employer@careerhub.test',
        'role' => UserRole::Employer->value,
        'is_active' => true,
    ]);
});

test('super admin cannot change their own role or deactivate themselves', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $target = User::factory()->employer()->create();

    $this->actingAs($superAdmin, 'filament');

    Livewire::test(ListUsers::class)
        ->assertActionHidden(TestAction::make('deactivate')->table($superAdmin))
        ->assertActionHidden(TestAction::make('changeRole')->table($superAdmin))
        ->assertActionHidden(TestAction::make('delete')->table($superAdmin))
        ->assertActionVisible(TestAction::make('deactivate')->table($target))
        ->assertActionVisible(TestAction::make('changeRole')->table($target));

    Livewire::test(EditUser::class, ['record' => $superAdmin->getRouteKey()])
        ->fillForm([
            'role' => UserRole::Employee->value,
            'is_active' => false,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($superAdmin->fresh())
        ->role->toBe(UserRole::SuperAdmin)
        ->is_active->toBeTrue();
});

test('employer cannot access the user management panel', function () {
    $employer = User::factory()->employer()->create();

    $this->actingAs($employer, 'filament')
        ->get('/super-admin/users')
        ->assertForbidden();

    Livewire::test(ListUsers::class)
        ->assertForbidden();
});

test('deactivated employer cannot create job postings', function () {
    $employer = User::factory()->employer()->create([
        'is_active' => false,
    ]);

    $this->actingAs($employer)
        ->get(route('employer.jobs.create'))
        ->assertForbidden();
});

test('super admin deactivation persists and blocks employer job creation', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $target = User::factory()->employer()->create(['is_active' => true]);

    $this->actingAs($superAdmin, 'filament');

    Livewire::test(ListUsers::class)
        ->callAction(TestAction::make('deactivate')->table($target))
        ->assertSuccessful();

    expect($target->fresh()->is_active)->toBeFalse();

    $this->actingAs($target->fresh())
        ->get(route('employer.jobs.create'))
        ->assertForbidden();
});
