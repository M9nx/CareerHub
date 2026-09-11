<?php

namespace Tests\Feature\Policies;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_employer_cannot_update_another_user(): void
    {
        $employer = User::factory()->create(['role' => UserRole::Employer]);
        $target = User::factory()->create();

        $this->assertFalse($employer->can('update', $target));
    }

    public function test_employer_cannot_view_any_users(): void
    {
        $employer = User::factory()->create(['role' => UserRole::Employer]);

        $this->assertFalse($employer->can('viewAny', User::class));
    }

    public function test_super_admin_can_update_any_user(): void
    {
        $superAdmin = User::factory()->create(['role' => UserRole::SuperAdmin]);
        $target = User::factory()->create();

        $this->assertTrue($superAdmin->can('update', $target));
    }

    public function test_super_admin_bypass_applies_via_gate_before(): void
    {
        // Confirms the Gate::before hook grants SuperAdmin access even
        // for abilities the policy itself doesn't explicitly enumerate.
        $superAdmin = User::factory()->create(['role' => UserRole::SuperAdmin]);

        $this->assertTrue($superAdmin->can('some-undefined-ability', User::class));
    }
}