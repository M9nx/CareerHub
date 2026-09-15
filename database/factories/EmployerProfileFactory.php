<?php

namespace Database\Factories;

use App\Models\EmployerProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployerProfileFactory extends Factory
{
    protected $model = EmployerProfile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'company_name' => fake()->company(),
            'industry' => fake()->optional()->bs(),
            'company_size' => fake()->optional()->randomElement(['1-10', '11-50', '51-200', '201-500', '500+']),
            'location' => fake()->optional()->city(),
            'website' => fake()->optional()->url(),
            'about' => fake()->optional()->paragraph(),
            'logo_path' => null,
        ];
    }
}
