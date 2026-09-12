<?php

namespace Tests\Feature\Profile;

use App\Models\EmployerProfile;
use App\Models\EmployeeProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_employer_has_one_employer_profile(): void
    {
        $user = User::factory()->create();

        $profile = EmployerProfile::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue(
            $user->employerProfile->is($profile)
        );
    }

    public function test_employee_has_one_employee_profile(): void
    {
        $user = User::factory()->create();

        $profile = EmployeeProfile::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue(
            $user->employeeProfile->is($profile)
        );
    }

    public function test_employer_profile_belongs_to_user(): void
    {
        $user = User::factory()->create();

        $profile = EmployerProfile::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue(
            $profile->user->is($user)
        );
    }

    public function test_employee_profile_belongs_to_user(): void
    {
        $user = User::factory()->create();

        $profile = EmployeeProfile::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue(
            $profile->user->is($user)
        );
    }
}