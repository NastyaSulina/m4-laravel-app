<?php

namespace Database\Seeders;

use App\Models\Selection;
use App\Models\Accommodation;
use App\Models\User;
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
        $users = User::factory(10)->create();
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $users->push($testUser);

        foreach ($users as $user) {
            Selection::factory(5)
                ->for($user, 'owner')
                ->has(Accommodation::factory(10)->for($user))
                ->create();
        }
    }
}
