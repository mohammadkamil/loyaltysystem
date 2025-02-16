<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PointTransaction>
 */
class PointTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => null, // Will be set dynamically
            'points' => $this->faker->numberBetween(10, 500), // ✅ Ensure points are always set
            'type' => $this->faker->randomElement(['add', 'deduct']), // ✅ Valid values
        ];
    }
}
