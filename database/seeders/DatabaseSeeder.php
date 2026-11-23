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
        $users = User::factory(1)->create();
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $users->push($testUser);

        foreach ($users as $user) {
            $selections = Selection::factory(3)
                ->for($user, 'owner')
                ->has(Accommodation::factory(3)->for($user))
                ->create();

            foreach ($selections as $selection) {
                $titles = collect(CriterionFactory::TITLES)->random(fake()->numberBetween(1, 5));

                foreach ($titles as $title) {
                    Criterion::factory()->for($selection)->create(['title' => $title]);
                }
            }
        }

        foreach ($users as $user) {
            $otherUsers = $users->except($user->id);

            if ($otherUsers->isEmpty()) {
                continue;
            }

            $owner = $otherUsers->random();
            $selectionId = $owner->selections()->inRandomOrder()->value('id');

            $user->sharedSelections()->attach($selectionId);
        }
    }
}
