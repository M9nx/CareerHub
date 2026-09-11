<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_employer_is_redirected_to_employer_dashboard_after_login(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Employer,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('employer.dashboard'));
    }

    public function test_employee_is_redirected_to_employee_dashboard_after_login(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Employee,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('employee.dashboard'));
    }
}