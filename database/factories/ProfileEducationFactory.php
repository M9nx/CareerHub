<?php

namespace Database\Factories;

use App\Models\ProfileEducation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProfileEducation>
 */
class ProfileEducationFactory extends Factory
{
    protected $model = ProfileEducation::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startedAt = fake()->optional()->dateTimeBetween('-15 years', '-4 years');

        return [
            'user_id' => User::factory(),
            'school' => fake()->company().' University',
            'degree' => fake()->optional()->randomElement(['BSc', 'MSc', 'BA', 'MBA']),
            'field' => fake()->optional()->randomElement(['Computer Science', 'Business', 'Design']),
            'started_at' => $startedAt,
            'ended_at' => $startedAt
                ? fake()->optional(0.8)->dateTimeBetween($startedAt, 'now')
                : null,
        ];
    }
}
