<?php

namespace Tests\Feature\JobPosting;

use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeJobBrowseTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_see_published_jobs(): void
    {
        $employee = User::factory()->employee()->create();
        $publishedJob = JobPosting::factory()->published()->create();

        $response = $this->actingAs($employee)
            ->get('/employee/jobs');

        $response->assertStatus(200);
        $response->assertSee($publishedJob->title);
    }

    public function test_employee_cannot_see_draft_jobs(): void
    {
        $employee = User::factory()->employee()->create();
        $draftJob = JobPosting::factory()->draft()->create();

        $response = $this->actingAs($employee)
            ->get('/employee/jobs');

        $response->assertStatus(200);
        $response->assertDontSee($draftJob->title);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/employee/jobs');

        $response->assertRedirect('/login');
    }

    public function test_employer_cannot_access_employee_jobs(): void
    {
        $employer = User::factory()->employer()->create();

        $this->actingAs($employer)
            ->get('/employee/jobs')
            ->assertForbidden();
    }
}