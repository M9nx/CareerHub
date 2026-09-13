<?php

namespace Tests\Feature\Post;

use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SharedFeedAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_employer_can_access_the_feed(): void
    {
        $employer = User::factory()->create([
            'role' => UserRole::Employer,
        ]);

        $response = $this->actingAs($employer)->get(route('feed.index'));

        $response->assertOk();
    }

    public function test_employee_can_access_the_feed(): void
    {
        $employee = User::factory()->create([
            'role' => UserRole::Employee,
        ]);

        $response = $this->actingAs($employee)->get(route('feed.index'));

        $response->assertOk();
    }

    public function test_feed_displays_published_posts(): void
    {
        $employee = User::factory()->create([
            'role' => UserRole::Employee,
        ]);

        Post::factory()->published()->create([
            'title' => 'Published Career Post',
            'author_id' => $employee->id,
            'author_role' => UserRole::Employee,
            'status' => PostStatus::Published,
            'is_active' => true,
        ]);

        $response = $this->actingAs($employee)->get(route('feed.index'));

        $response
            ->assertOk()
            ->assertSee('Published Career Post');
    }
}
