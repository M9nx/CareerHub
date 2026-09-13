<?php

namespace Tests\Feature\Profile;

use App\Models\EmployerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployerProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_employer_can_update_company_name(): void
    {
        $user = User::factory()->employer()->create();

        EmployerProfile::factory()->create([
            'user_id' => $user->id,
            'company_name' => 'Old Company',
        ]);

        $response = $this->actingAs($user)->patch(
            route('employer.profile.update'),
            [
                'company_name' => 'New Company',
            ]
        );

        $response
            ->assertRedirect(route('employer.profile.edit'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('employer_profiles', [
            'user_id' => $user->id,
            'company_name' => 'New Company',
        ]);
    }
}
