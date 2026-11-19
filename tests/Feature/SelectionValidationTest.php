<?php

use App\Models\Selection;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

test('selection creation rejects invalid input', function (array $invalid, string $field) {
    $user = User::factory()->create();
    actingAs($user);

    post(route('userzone.selections.store'), array_replace([
        'title' => 'Madrid trip',
        'search_type' => 'short_stay',
    ], $invalid))->assertSessionHasErrors($field);

    expect($user->selections()->count())->toBe(0);
})->with([
    'short title' => [['title' => 'Hi'], 'title'],
    'unknown type' => [['search_type' => 'unknown'], 'search_type'],
    'invalid date' => [['start_date' => 'not a date'], 'start_date'],
    'reversed dates' => [['start_date' => '2027-06-10', 'end_date' => '2027-06-09'], 'end_date'],
]);

test('selection update rejects reversed dates without saving changes', function () {
    $selection = Selection::factory()->create(['title' => 'Original', 'start_date' => null, 'end_date' => null]);
    actingAs($selection->owner);

    put(route('userzone.selections.update', $selection), [
        'title' => 'Changed',
        'search_type' => 'short_stay',
        'start_date' => '2027-06-10',
        'end_date' => '2027-06-09',
    ])->assertSessionHasErrors('end_date');

    expect($selection->fresh()->title)->toBe('Original');
    expect($selection->fresh()->start_date)->toBeNull();
    expect($selection->fresh()->end_date)->toBeNull();
});

test('selection forms accept optional dates', function (?string $start, ?string $end) {
    $user = User::factory()->create();
    actingAs($user);
    $input = ['title' => 'Madrid trip', 'search_type' => 'short_stay', 'start_date' => $start, 'end_date' => $end];

    post(route('userzone.selections.store'), $input)->assertSessionHasNoErrors()
        ->assertRedirect(route('userzone.selections.index'));
    $selection = $user->selections()->sole();
    expect($selection->start_date)->toBe($start);
    expect($selection->end_date)->toBe($end);

    put(route('userzone.selections.update', $selection), [...$input, 'title' => 'Updated trip'])
        ->assertSessionHasNoErrors()->assertRedirect(route('userzone.selections.index'));
    expect($selection->fresh()->title)->toBe('Updated trip');
    expect($selection->fresh()->start_date)->toBe($start);
    expect($selection->fresh()->end_date)->toBe($end);
})->with([
    'no dates' => [null, null],
    'start only' => ['2027-06-10', null],
    'end only' => [null, '2027-06-10'],
    'equal dates' => ['2027-06-10', '2027-06-10'],
]);
