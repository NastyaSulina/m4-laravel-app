<?php

namespace Database\Seeders;

use App\Models\Accommodation;
use App\Models\Criterion;
use App\Models\Selection;
use App\Models\User;
use Database\Factories\CriterionFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory(5)->create();

        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $selections = Selection::factory(5)->for($testUser, 'owner')->create();

        foreach ($selections->take(3) as $selection) {
            $selection->users()->attach($users->random(2));
        }

        foreach ($users as $user) {
            $selection = Selection::factory()->for($user, 'owner')->create();
            $selection->users()->attach($testUser);
            $selections->push($selection);
        }

        foreach ($selections as $selection) {
            $titles = collect(CriterionFactory::TITLES)->random(fake()->numberBetween(3, 5));

            foreach ($titles as $title) {
                Criterion::factory()->for($selection)->create(['title' => $title]);
            }

            $criteria = $selection->criteria;
            $contributors = $selection->users()->get();
            $contributors->push($selection->owner);

            for ($i = 0; $i < 5; $i++) {
                $accommodation = Accommodation::factory()
                    ->for($selection)
                    ->for($contributors->random(), 'user')
                    ->create();

                foreach ($criteria as $criterion) {
                    $accommodation->criteria()->attach($criterion, [
                        'is_met' => fake()->randomElement([true, false, null]),
                    ]);
                }
            }
        }
    }
}
