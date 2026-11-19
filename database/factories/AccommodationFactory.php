<?php

namespace Database\Factories;

use App\Models\Accommodation;
use App\Models\Selection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Accommodation>
 */
class AccommodationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'selection_id' => Selection::factory(),
            'title' => fake()->city().' apartment',
            'source_url' => fake()->url(),
            'address' => fake()->address(),
            'status' => fake()->randomElement(['considering', 'shortlisted', 'selected', 'rejected', 'unavailable']),
            'property_type' => fake()->randomElement(['apartment', 'house', 'room', 'hotel']),
            'note' => fake()->boolean() ? fake()->sentence() : null,
        ];
    }
}
