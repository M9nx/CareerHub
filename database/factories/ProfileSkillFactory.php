<?php

namespace Database\Factories;

use App\Models\ProfileSkill;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProfileSkill>
 */
class ProfileSkillFactory extends Factory
{
    protected $model = ProfileSkill::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->unique()->word(),
            'sort' => fake()->numberBetween(0, 100),
        ];
    }
}
