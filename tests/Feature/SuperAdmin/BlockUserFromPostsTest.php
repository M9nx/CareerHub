<?php

use App\Filament\SuperAdmin\Resources\UserResource\Pages\ListUsers;
use App\Models\ActivityLog;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel('super-admin');
});

test('super admin can block a user from posts', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $user = User::factory()->employee()->create([
        'is_blocked_from_posts' => false,
    ]);

    $this->actingAs($superAdmin, 'filament');

    Livewire::test(ListUsers::class)
        ->callAction(TestAction::make('blockFromPosts')->table($user))
        ->assertSuccessful();

    expect($user->fresh()->isBlockedFromPosts())->toBeTrue();

    $this->assertDatabaseHas('activity_logs', [
        'log_name' => 'user.blocked_from_posts',
        'subject_type' => $user->getMorphClass(),
        'subject_id' => $user->id,
        'causer_id' => $superAdmin->id,
    ]);

    $log = ActivityLog::query()
        ->where('log_name', 'user.blocked_from_posts')
        ->where('subject_id', $user->id)
        ->first();

    expect($log->properties)->toMatchArray([
        'event' => 'blocked_from_posts',
        'is_blocked_from_posts' => true,
    ]);
});

test('super admin can unblock a user from posts', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $user = User::factory()->employee()->create([
        'is_blocked_from_posts' => true,
    ]);

    $this->actingAs($superAdmin, 'filament');

    Livewire::test(ListUsers::class)
        ->callAction(TestAction::make('unblockFromPosts')->table($user))
        ->assertSuccessful();

    expect($user->fresh()->isBlockedFromPosts())->toBeFalse();
});

test('super admin cannot block themselves from posts', function () {
    $superAdmin = User::factory()->superAdmin()->create([
        'is_blocked_from_posts' => false,
    ]);

    $this->actingAs($superAdmin, 'filament');

    Livewire::test(ListUsers::class)
        ->assertActionHidden(TestAction::make('blockFromPosts')->table($superAdmin));
});

test('employer cannot access the users list to block from posts', function () {
    $employer = User::factory()->employer()->create();

    $this->actingAs($employer, 'filament')
        ->get(ListUsers::getUrl())
        ->assertForbidden();
});
