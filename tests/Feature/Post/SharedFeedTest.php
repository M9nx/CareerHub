<?php

namespace Tests\Feature\Post;

use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SharedFeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_feed_shows_published_employer_and_employee_posts(): void
    {
        $employer = User::factory()->create([
            'role' => UserRole::Employer,
        ]);

        $employee = User::factory()->create([
            'role' => UserRole::Employee,
        ]);

        Post::factory()->published()->create([
            'author_id' => $employer->id,
            'author_role' => UserRole::Employer,
            'title' => 'Employer Published Post',
            'body' => 'Employer post body.',
            'status' => PostStatus::Published,
            'is_active' => true,
        ]);

        Post::factory()->published()->create([
            'author_id' => $employee->id,
            'author_role' => UserRole::Employee,
            'title' => 'Employee Published Post',
            'body' => 'Employee post body.',
            'status' => PostStatus::Published,
            'is_active' => true,
        ]);

        $response = $this->actingAs($employee)->get(route('feed.index'));

        $response
            ->assertOk()
            ->assertSee('Employer Published Post')
            ->assertSee('Employee Published Post');
    }

    public function test_feed_does_not_show_unpublished_or_inactive_posts(): void
    {
        $employee = User::factory()->create([
            'role' => UserRole::Employee,
        ]);

        Post::factory()->create([
            'author_id' => $employee->id,
            'author_role' => UserRole::Employee,
            'title' => 'Draft Post',
            'status' => PostStatus::Draft,
            'is_active' => true,
        ]);

        Post::factory()->published()->create([
            'author_id' => $employee->id,
            'author_role' => UserRole::Employee,
            'title' => 'Inactive Post',
            'status' => PostStatus::Published,
            'is_active' => false,
        ]);

        $response = $this->actingAs($employee)->get(route('feed.index'));

        $response
            ->assertOk()
            ->assertDontSee('Draft Post')
            ->assertDontSee('Inactive Post');
    }

    public function test_feed_is_paginated(): void
    {
        $employee = User::factory()->create([
            'role' => UserRole::Employee,
        ]);

        Post::factory()
            ->published()
            ->count(11)
            ->create([
                'author_id' => $employee->id,
                'author_role' => UserRole::Employee,
                'is_active' => true,
            ]);

        $response = $this->actingAs($employee)->get(route('feed.index'));

        $response
            ->assertOk()
            ->assertSee('page=2');
    }
}