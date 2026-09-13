<?php

namespace Database\Factories;

use App\Enums\PostStatus;
use App\Enums\UserRole;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $authorRole = fake()->randomElement([
            UserRole::Employer,
            UserRole::Employee,
        ]);

        return [
            'author_id' => User::factory()
                ->state(['role' => $authorRole]),
            'author_role' => $authorRole,
            'title' => fake()->sentence(6),
            'body' => fake()->paragraphs(3, true),
            'status' => PostStatus::Draft,
            'is_active' => true,
        ];
    }

    /**
     * Create a published post.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PostStatus::Published,
        ]);
    }
}