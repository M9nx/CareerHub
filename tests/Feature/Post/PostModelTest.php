<?php

namespace Tests\Feature\Post;

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_factory_persists_published_post(): void
    {
        $post = Post::factory()->published()->create();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'author_id' => $post->author_id,
            'author_role' => $post->author_role->value,
            'title' => $post->title,
            'body' => $post->body,
            'status' => PostStatus::Published->value,
            'is_active' => true,
        ]);
    }
}