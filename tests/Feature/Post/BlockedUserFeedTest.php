<?php

namespace Tests\Feature\Post;

use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlockedUserFeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_blocked_employee_can_view_feed_but_cannot_create_post(): void
    {
        $blockedEmployee = User::factory()->create([
            'role' => UserRole::Employee,
            'is_blocked_from_posts' => true,
        ]);

        Post::factory()->published()->create([
            'author_role' => UserRole::Employer,
            'status' => PostStatus::Published,
            'is_active' => true,
            'title' => 'Published Employer Post',
        ]);

        $feedResponse = $this->actingAs($blockedEmployee)
            ->get(route('feed.index'));

        $feedResponse
            ->assertOk()
            ->assertSee('Published Employer Post')
            ->assertDontSee('Create Post');

        $createResponse = $this->actingAs($blockedEmployee)
            ->post(route('employee.posts.store'), [
                'title' => 'Blocked Post',
                'body' => 'This post should not be created.',
                'publish' => true,
            ]);

        $createResponse
            ->assertRedirect()
            ->assertSessionHas('error', 'You are blocked from creating posts.');

        $this->assertDatabaseMissing('posts', [
            'title' => 'Blocked Post',
        ]);
    }

    public function test_blocked_employer_can_view_feed_but_cannot_create_post(): void
    {
        $blockedEmployer = User::factory()->create([
            'role' => UserRole::Employer,
            'is_blocked_from_posts' => true,
        ]);

        Post::factory()->published()->create([
            'author_role' => UserRole::Employee,
            'status' => PostStatus::Published,
            'is_active' => true,
            'title' => 'Published Employee Post',
        ]);

        $feedResponse = $this->actingAs($blockedEmployer)
            ->get(route('feed.index'));

        $feedResponse
            ->assertOk()
            ->assertSee('Published Employee Post')
            ->assertDontSee('Create Post');

        $createResponse = $this->actingAs($blockedEmployer)
            ->post(route('employer.posts.store'), [
                'title' => 'Blocked Employer Post',
                'body' => 'This post should not be created.',
                'publish' => true,
            ]);

        $createResponse
            ->assertRedirect()
            ->assertSessionHas('error', 'You are blocked from creating posts.');

        $this->assertDatabaseMissing('posts', [
            'title' => 'Blocked Employer Post',
        ]);
    }
}