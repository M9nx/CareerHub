<?php

namespace Tests\Feature\Post;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_blocked_user_cannot_create_post(): void
    {
        $user = User::factory()->create([
            'is_blocked_from_posts' => true,
        ]);

        $post = Post::factory()->make([
            'author_id' => $user->id,
        ]);

        $this->assertFalse(
            $user->can('create', [Post::class, $post])
        );
    }

    public function test_unblocked_user_can_create_post(): void
    {
        $user = User::factory()->create([
            'is_blocked_from_posts' => false,
        ]);

        $this->assertTrue(
            $user->can('create', Post::class)
        );
    }

    public function test_user_can_update_own_post(): void
    {
        $user = User::factory()->create([
            'is_blocked_from_posts' => false,
        ]);

        $post = Post::factory()->create([
            'author_id' => $user->id,
        ]);

        $this->assertTrue($user->can('update', $post));
    }

    public function test_user_cannot_update_another_users_post(): void
    {
        $user = User::factory()->create();

        $post = Post::factory()->create();

        $this->assertFalse($user->can('update', $post));
    }

    public function test_user_can_delete_own_post(): void
    {
        $user = User::factory()->create();

        $post = Post::factory()->create([
            'author_id' => $user->id,
        ]);

        $this->assertTrue($user->can('delete', $post));
    }

    public function test_user_cannot_delete_another_users_post(): void
    {
        $user = User::factory()->create();

        $post = Post::factory()->create();

        $this->assertFalse($user->can('delete', $post));
    }
}