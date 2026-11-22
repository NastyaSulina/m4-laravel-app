<?php

use App\Models\Selection;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\seed;

test('factory creates a selection for an existing owner without adding users', function () {
    $owner = User::factory()->create();

    $selection = Selection::factory()->for($owner, 'owner')->create();

    assertModelExists($selection);
    assertDatabaseCount('users', 1);
    expect($selection->owner->id)->toBe($owner->id);
    if ($selection->start_date !== null && $selection->end_date !== null) {
        expect(strtotime($selection->end_date) > strtotime($selection->start_date))->toBeTrue();
    }
});

test('selection can be saved with only required fields', function () {
    $selection = new Selection;
    $selection->owner()->associate(User::factory()->create());
    $selection->title = 'Summer in Madrid';
    $selection->search_type = 'short_stay';
    $selection->save();
    $selection->refresh();

    expect($selection->status)->toBe('active');
    expect($selection->start_date)->toBeNull();
    expect($selection->end_date)->toBeNull();
});

test('database seeder creates selections with accommodations and criteria for its two users', function () {
    seed(DatabaseSeeder::class);

    assertDatabaseCount('users', 2);
    assertDatabaseCount('selections', 6);
    assertDatabaseCount('accommodations', 18);
    foreach (User::all() as $user) {
        expect($user->selections()->count())->toBe(3);
    }
    foreach (Selection::all() as $selection) {
        $titles = $selection->criteria()->pluck('title');
        expect($titles->count())->toBeBetween(1, 5);
        expect($titles->unique()->count())->toBe($titles->count());
        expect($selection->accommodations()->count())->toBe(3);
        expect($selection->accommodations()->where('user_id', $selection->owner_id)->count())->toBe(3);
    }
});
