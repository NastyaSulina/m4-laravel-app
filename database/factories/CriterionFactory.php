<?php

namespace Database\Factories;

use App\Models\Criterion;
use App\Models\Selection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Criterion>
 */
class CriterionFactory extends Factory
{
    public const TITLES = [
        'Good Wi-Fi',
        'Balcony',
        'Parking',
        'Air conditioning',
        'Pet friendly',
        'Parrots welcome',
        'Walking distance to Zara',
        'Sea view',
        'Quiet area',
        'Near metro',
        'Dishwasher',
        'Lift',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'selection_id' => Selection::factory(),
            'title' => fake()->randomElement(self::TITLES),
        ];
    }
}
