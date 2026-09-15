<?php

namespace Database\Factories;

use App\Models\ProfileExperience;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProfileExperience>
 */
class ProfileExperienceFactory extends Factory
{
    protected $model = ProfileExperience::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startedAt = fake()->dateTimeBetween('-10 years', '-1 year');

        return [
            'user_id' => User::factory(),
            'title' => fake()->jobTitle(),
            'company' => fake()->company(),
            'location' => fake()->optional()->city(),
            'started_at' => $startedAt,
            'ended_at' => fake()->optional(0.6)->dateTimeBetween($startedAt, 'now'),
            'description' => fake()->optional()->paragraph(),
        ];
    }
}
