<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceAvailability>
 */
class ServiceAvailabilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'date' => $this->faker->dateTimeBetween('+1 day', '+1 month')->format('Y-m-d'),
            'price' => $this->faker->numberBetween(1000, 5000),
            'available_slots' => $this->faker->numberBetween(1, 10),
            'is_available' => true,
        ];
    }
}
