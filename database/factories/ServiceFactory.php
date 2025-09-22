<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => rtrim($this->faker->sentence(3), '.'),
            'description' => $this->faker->paragraph(5),
            'price' => $this->faker->numberBetween(1000, 10000),
            'is_active' => $this->faker->boolean(90),
            'partner_id' => User::factory(),
            'type' => $this->faker->randomElement(['standard', 'premium', 'vip']),
        ];
    }
}
