<?php

namespace Tests\Feature\Post;

use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployerPostCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_employer_can_create_a_published_post(): void
    {
        $employer = User::factory()->create([
            'role' => UserRole::Employer,
        ]);

        $response = $this->actingAs($employer)->post(
            route('employer.posts.store'),
            [
                'title' => 'Employer Career Post',
                'body' => 'This is an employer career post.',
                'publish' => '1',
            ]
        );

        $response
            ->assertRedirect(route('employer.posts.index'))
            ->assertSessionHas('success', 'Post created successfully.');

        $this->assertDatabaseHas('posts', [
            'author_id' => $employer->id,
            'author_role' => UserRole::Employer->value,
            'title' => 'Employer Career Post',
            'body' => 'This is an employer career post.',
            'status' => PostStatus::Published->value,
            'is_active' => true,
        ]);
    }

    public function test_published_employer_post_is_visible_on_shared_feed(): void
    {
        $employer = User::factory()->create([
            'role' => UserRole::Employer,
        ]);

        Post::factory()->published()->create([
            'author_id' => $employer->id,
            'author_role' => UserRole::Employer,
            'title' => 'Employer Feed Post',
            'status' => PostStatus::Published,
            'is_active' => true,
        ]);

        $response = $this->actingAs($employer)->get(route('feed.index'));

        $response
            ->assertOk()
            ->assertSee('Employer Feed Post');
    }

    public function test_employer_can_update_own_post(): void
    {
        $employer = User::factory()->create([
            'role' => UserRole::Employer,
        ]);

        $post = Post::factory()->create([
            'author_id' => $employer->id,
            'author_role' => UserRole::Employer,
            'title' => 'Old Employer Post',
        ]);

        $response = $this->actingAs($employer)->put(
            route('employer.posts.update', $post),
            [
                'title' => 'Updated Employer Post',
                'body' => 'Updated employer post body.',
                'publish' => '1',
            ]
        );

        $response->assertRedirect(route('employer.posts.index'));

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'author_id' => $employer->id,
            'title' => 'Updated Employer Post',
            'body' => 'Updated employer post body.',
            'status' => PostStatus::Published->value,
        ]);
    }

    public function test_employer_can_delete_own_post(): void
    {
        $employer = User::factory()->create([
            'role' => UserRole::Employer,
        ]);

        $post = Post::factory()->create([
            'author_id' => $employer->id,
            'author_role' => UserRole::Employer,
        ]);

        $response = $this->actingAs($employer)->delete(
            route('employer.posts.destroy', $post)
        );

        $response->assertRedirect(route('employer.posts.index'));

        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
        ]);
    }

    public function test_employer_cannot_update_another_users_post(): void
    {
        $employer = User::factory()->create([
            'role' => UserRole::Employer,
        ]);

        $otherEmployer = User::factory()->create([
            'role' => UserRole::Employer,
        ]);

        $post = Post::factory()->create([
            'author_id' => $otherEmployer->id,
            'author_role' => UserRole::Employer,
        ]);

        $response = $this->actingAs($employer)->put(
            route('employer.posts.update', $post),
            [
                'title' => 'Unauthorized Update',
                'body' => 'This update should not be allowed.',
                'publish' => '1',
            ]
        );

        $response->assertForbidden();

        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
            'title' => 'Unauthorized Update',
        ]);
    }
}
