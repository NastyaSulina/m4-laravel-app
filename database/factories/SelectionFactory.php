<?php

namespace Database\Factories;

use App\Models\Selection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Selection>
 */
class SelectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = today()->addDays(fake()->numberBetween(1, 180));
        $endDate = $startDate->copy()->addDays(fake()->numberBetween(1, 30));

        return [
            'owner_id' => User::factory(),
            'title' => fake()->city().' accommodation',
            'search_type' => fake()->randomElement(['short_stay', 'long_term']),
            'status' => fake()->randomElement(['active', 'archived']),
            'start_date' => fake()->boolean() ? $startDate->toDateString() : null,
            'end_date' => fake()->boolean() ? $endDate->toDateString() : null,
        ];
    }
}
