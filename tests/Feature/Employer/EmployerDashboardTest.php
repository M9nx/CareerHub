<?php

namespace Tests\Feature\Employer;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployerDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_employer_can_access_dashboard(): void
    {
        $employer = User::factory()->employer()->create();

        $response = $this->actingAs($employer)
            ->get('/employer/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Welcome to the Employer Dashboard');
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/employer/dashboard');

        $response->assertRedirect('/login');
    }
}
