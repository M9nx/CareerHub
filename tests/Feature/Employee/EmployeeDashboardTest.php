<?php

namespace Tests\Feature\Employee;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_access_dashboard(): void
    {
        $employee = User::factory()->employee()->create();

        $response = $this->actingAs($employee)
            ->get('/employee/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Welcome to the Employee Dashboard');
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/employee/dashboard');

        $response->assertRedirect('/login');
    }
}