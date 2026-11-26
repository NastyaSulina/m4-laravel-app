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
    /** @var list<string> */
    private const NOTES = [
        'Ask whether the bedroom faces the street or the courtyard.',
        'Check the Wi-Fi speed before booking; we will be working remotely.',
        'Confirm whether cleaning and utilities are included in the price.',
        'The photos look promising, but we need to check how much natural light there is.',
        'Ask about late check-in and where to collect the keys.',
        'Compare the cancellation policy with the other options before deciding.',
    ];

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
            'source_url' => fake()->url(),
            'address' => fake()->address(),
            'status' => fake()->randomElement(['considering', 'selected', 'rejected']),
            'property_type' => fake()->randomElement(['apartment', 'house', 'room', 'hotel']),
            'title' => fn (array $attributes): string => fake()->randomElement(['Little', 'Cosy', 'Quiet', 'Lovely', 'Bright', 'Suspiciously cheap'])
                .' '.$attributes['property_type'].' '
                .fake()->randomElement(['by the sea', 'near the metro', 'with a view', 'with a quirky host', 'above a noisy bar', 'in the middle of nowhere']),
            'note' => fake()->boolean() ? fake()->randomElement(self::NOTES) : null,
        ];
    }
}
