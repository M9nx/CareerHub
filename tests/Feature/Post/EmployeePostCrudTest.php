<?php

namespace Tests\Feature\Post;

use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeePostCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_create_a_published_post(): void
    {
        $employee = User::factory()->create([
            'role' => UserRole::Employee,
        ]);

        $response = $this->actingAs($employee)->post(
            route('employee.posts.store'),
            [
                'title' => 'Employee Career Post',
                'body' => 'This is an employee career post.',
                'publish' => '1',
            ]
        );

        $response
            ->assertRedirect(route('employee.posts.index'))
            ->assertSessionHas('success', 'Post created successfully.');

        $this->assertDatabaseHas('posts', [
            'author_id' => $employee->id,
            'author_role' => UserRole::Employee->value,
            'title' => 'Employee Career Post',
            'body' => 'This is an employee career post.',
            'status' => PostStatus::Published->value,
            'is_active' => true,
        ]);
    }

    public function test_published_employee_post_is_visible_on_shared_feed(): void
    {
        $employee = User::factory()->create([
            'role' => UserRole::Employee,
        ]);

        Post::factory()->published()->create([
            'author_id' => $employee->id,
            'author_role' => UserRole::Employee,
            'title' => 'Employee Feed Post',
            'status' => PostStatus::Published,
            'is_active' => true,
        ]);

        $response = $this->actingAs($employee)->get(route('feed.index'));

        $response
            ->assertOk()
            ->assertSee('Employee Feed Post');
    }

    public function test_employee_can_update_own_post(): void
    {
        $employee = User::factory()->create([
            'role' => UserRole::Employee,
        ]);

        $post = Post::factory()->create([
            'author_id' => $employee->id,
            'author_role' => UserRole::Employee,
            'title' => 'Old Employee Post',
        ]);

        $response = $this->actingAs($employee)->put(
            route('employee.posts.update', $post),
            [
                'title' => 'Updated Employee Post',
                'body' => 'Updated employee post body.',
                'publish' => '1',
            ]
        );

        $response->assertRedirect(route('employee.posts.index'));

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'author_id' => $employee->id,
            'title' => 'Updated Employee Post',
            'body' => 'Updated employee post body.',
            'status' => PostStatus::Published->value,
        ]);
    }

    public function test_employee_can_delete_own_post(): void
    {
        $employee = User::factory()->create([
            'role' => UserRole::Employee,
        ]);

        $post = Post::factory()->create([
            'author_id' => $employee->id,
            'author_role' => UserRole::Employee,
        ]);

        $response = $this->actingAs($employee)->delete(
            route('employee.posts.destroy', $post)
        );

        $response->assertRedirect(route('employee.posts.index'));

        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
        ]);
    }

    public function test_employee_cannot_update_another_users_post(): void
    {
        $employee = User::factory()->create([
            'role' => UserRole::Employee,
        ]);

        $otherEmployee = User::factory()->create([
            'role' => UserRole::Employee,
        ]);

        $post = Post::factory()->create([
            'author_id' => $otherEmployee->id,
            'author_role' => UserRole::Employee,
        ]);

        $response = $this->actingAs($employee)->put(
            route('employee.posts.update', $post),
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
