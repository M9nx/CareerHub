<?php
use App\Enums\UserRole;
use App\Filament\SuperAdmin\Resources\UserResource;
use App\Models\User;
use Livewire\Livewire;
it('can block a user from posts as super admin', function () {
    $superAdmin = User::factory()->create([
        'role' => UserRole::SuperAdmin,
    ]);
    $user = User::factory()->create([
        'is_blocked_from_posts' => false,
    ]);
    $this->actingAs($superAdmin);
    Livewire::test(UserResource\Pages\ListUsers::class)
        ->callTableAction('blockFromPosts', $user)
        ->assertSuccessful();
    expect($user->refresh()->is_blocked_from_posts)->toBeTrue();
});
it('can unblock a user from posts as super admin', function () {
    $superAdmin = User::factory()->create([
        'role' => UserRole::SuperAdmin,
    ]);
    $user = User::factory()->create([
        'is_blocked_from_posts' => true,
    ]);
    $this->actingAs($superAdmin);
    Livewire::test(UserResource\Pages\ListUsers::class)
        ->callTableAction('unblockFromPosts', $user)
        ->assertSuccessful();
    expect($user->refresh()->is_blocked_from_posts)->toBeFalse();
});
it('cannot block itself from posts', function () {
    $superAdmin = User::factory()->create([
        'role' => UserRole::SuperAdmin,
        'is_blocked_from_posts' => false,
    ]);
    $this->actingAs($superAdmin);
    // The action is hidden for the acting user's own record, so it
    // shouldn't even be callable against themselves.
    Livewire::test(UserResource\Pages\ListUsers::class)
        ->assertTableActionHidden('blockFromPosts', $superAdmin);
});