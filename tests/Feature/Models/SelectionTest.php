<?php

use App\Models\Selection;
use Database\Seeders\DatabaseSeeder;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\seed;

test('factory creates a selection without a user', function () {
    $selection = Selection::factory()->create();

    assertModelExists($selection);
    assertDatabaseCount('users', 0);
    if ($selection->start_date !== null && $selection->end_date !== null) {
        expect(strtotime($selection->end_date) > strtotime($selection->start_date))->toBeTrue();
    }
});

test('selection can be saved with only required fields', function () {
    $selection = new Selection;
    $selection->title = 'Summer in Madrid';
    $selection->search_type = 'trip';
    $selection->save();
    $selection->refresh();

    expect($selection->status)->toBe('active');
    expect($selection->start_date)->toBeNull();
    expect($selection->end_date)->toBeNull();
});

test('selection preserves its dates', function (?string $startDate, ?string $endDate) {
    $selection = Selection::factory()->create([
        'title' => 'Summer in Madrid',
        'search_type' => 'trip',
        'start_date' => $startDate,
        'end_date' => $endDate,
    ])->refresh();

    expect($selection->start_date)->toBe($startDate);
    expect($selection->end_date)->toBe($endDate);
})->with([
    'both dates' => ['2027-06-01', '2027-06-15'],
    'start only' => ['2027-06-01', null],
    'end only' => [null, '2027-06-15'],
    'neither date' => [null, null],
]);

test('database seeder creates five selections', function () {
    seed(DatabaseSeeder::class);

    assertDatabaseCount('selections', 5);
    assertDatabaseCount('users', 1);
});
