<?php

namespace Tests\Feature\Models;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleCastTest extends TestCase
{
    use RefreshDatabase;

    public function test_employer_factory_state_persists_employer_role(): void
    {
        $user = User::factory()->employer()->create();

        $this->assertInstanceOf(UserRole::class, $user->fresh()->role);
        $this->assertSame(UserRole::Employer, $user->fresh()->role);
    }

    public function test_is_active_defaults_to_true(): void
    {
        $user = User::factory()->create();

        $this->assertTrue($user->is_active);
    }

    public function test_super_admin_can_access_super_admin_panel(): void
    {
        $user = User::factory()->superAdmin()->create();

        $this->assertTrue($user->canAccessPanel(Filament::getPanel('super-admin')));
    }

    public function test_employer_cannot_access_super_admin_panel(): void
    {
        $user = User::factory()->employer()->create();

        $this->assertFalse($user->canAccessPanel(Filament::getPanel('super-admin')));
    }
}