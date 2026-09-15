<?php

namespace Database\Factories;

use App\Enums\ConnectionStatus;
use App\Models\Connection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Connection>
 */
class ConnectionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'requester_id' => User::factory()->employee(),
            'addressee_id' => User::factory()->employer(),
            'status' => ConnectionStatus::Pending,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ConnectionStatus::Pending,
        ]);
    }

    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ConnectionStatus::Accepted,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ConnectionStatus::Rejected,
        ]);
    }
}
