<?php

namespace Tests\Feature\Policies;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_employer_cannot_update_another_user(): void
    {
        $employer = User::factory()->employer()->create();
        $target = User::factory()->employee()->create();

        $this->assertFalse($employer->can('update', $target));
    }

    public function test_employer_cannot_view_any_users(): void
    {
        $employer = User::factory()->employer()->create();

        $this->assertFalse($employer->can('viewAny', User::class));
    }

    public function test_super_admin_can_update_any_user(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $target = User::factory()->employee()->create();

        $this->assertTrue($superAdmin->can('update', $target));
    }

    public function test_super_admin_bypass_applies_via_gate_before(): void
    {
        // Confirms the Gate::before hook grants SuperAdmin access even
        // for abilities the policy itself doesn't explicitly enumerate.
        $superAdmin = User::factory()->superAdmin()->create();

        $this->assertTrue($superAdmin->can('some-undefined-ability', User::class));
    }
}
