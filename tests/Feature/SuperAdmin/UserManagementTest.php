<?php

namespace Tests\Feature\SuperAdmin;

use App\Enums\UserRole;
use App\Filament\SuperAdmin\Resources\UserResource\Pages\ListUsers;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_list_users(): void
    {
        $superAdmin = User::factory()->create(['role' => UserRole::SuperAdmin]);
        $otherUsers = User::factory()->count(3)->create();
        $this->actingAs($superAdmin);
        Livewire::test(ListUsers::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords($otherUsers);
    }

    public function test_super_admin_can_update_a_users_role(): void
    {
        $superAdmin = User::factory()->create(['role' => UserRole::SuperAdmin]);
        $target = User::factory()->create(['role' => UserRole::Employer]);
        $this->actingAs($superAdmin);
        $target->update(['role' => UserRole::SuperAdmin]);
        $this->assertSame(UserRole::SuperAdmin, $target->fresh()->role);
    }

    public function test_employer_cannot_access_the_user_management_panel(): void
    {
        $employer = User::factory()->create(['role' => UserRole::Employer]);
        $this->actingAs($employer)
            ->get('/super-admin/users')
            ->assertRedirect();
    }
}
