<?php

use App\Models\Selection;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

test('selection creation accepts status but ignores supplied ownership and id', function (string $status) {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    actingAs($user);

    post(route('userzone.selections.store'), [
        'title' => 'Madrid trip',
        'search_type' => 'short_stay',
        'owner_id' => $otherUser->id,
        'id' => 999,
        'status' => $status,
    ])->assertSessionHasNoErrors()->assertRedirect(route('userzone.selections.index'));

    $selection = $user->selections()->sole();
    expect($selection->id)->not->toBe(999);
    assertDatabaseHas('selections', [
        'id' => $selection->id,
        'owner_id' => $user->id,
        'title' => 'Madrid trip',
        'search_type' => 'short_stay',
        'status' => $status,
        'start_date' => null,
        'end_date' => null,
    ]);
})->with(['active', 'archived']);

test('selection update accepts status but ignores protected fields and clears omitted dates', function (string $status) {
    $selection = Selection::factory()->create([
        'status' => $status === 'active' ? 'archived' : 'active',
        'start_date' => '2027-06-01',
        'end_date' => '2027-06-10',
    ]);
    $otherUser = User::factory()->create();
    actingAs($selection->owner);

    put(route('userzone.selections.update', $selection), [
        'title' => 'Updated trip',
        'search_type' => 'long_term',
        'owner_id' => $otherUser->id,
        'id' => 999,
        'status' => $status,
    ])->assertSessionHasNoErrors()->assertRedirect(route('userzone.selections.index'));

    assertDatabaseHas('selections', [
        'id' => $selection->id,
        'owner_id' => $selection->owner_id,
        'title' => 'Updated trip',
        'search_type' => 'long_term',
        'status' => $status,
        'start_date' => null,
        'end_date' => null,
    ]);
})->with(['active', 'archived']);

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
    'empty status' => [['status' => ''], 'status'],
    'null status' => [['status' => null], 'status'],
    'unknown status' => [['status' => 'unknown'], 'status'],
    'array status' => [['status' => ['active']], 'status'],
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

test('selection update rejects invalid status without saving changes', function (mixed $status) {
    $selection = Selection::factory()->create(['title' => 'Original', 'status' => 'active']);
    actingAs($selection->owner);

    put(route('userzone.selections.update', $selection), [
        'title' => 'Changed',
        'search_type' => 'short_stay',
        'status' => $status,
    ])->assertSessionHasErrors('status');

    assertDatabaseHas('selections', [
        'id' => $selection->id,
        'title' => 'Original',
        'status' => 'active',
    ]);
})->with([
    'empty' => [''],
    'null' => [null],
    'unknown' => ['unknown'],
    'array' => [['active']],
]);

test('selection creation defaults to active when status is omitted', function () {
    $user = User::factory()->create();
    actingAs($user);

    post(route('userzone.selections.store'), [
        'title' => 'Madrid trip',
        'search_type' => 'short_stay',
    ])->assertSessionHasNoErrors()->assertRedirect(route('userzone.selections.index'));

    expect($user->selections()->sole()->status)->toBe('active');
});

test('selection update preserves archived status when status is omitted', function () {
    $selection = Selection::factory()->create(['status' => 'archived']);
    actingAs($selection->owner);

    put(route('userzone.selections.update', $selection), [
        'title' => 'Updated trip',
        'search_type' => 'long_term',
    ])->assertSessionHasNoErrors()->assertRedirect(route('userzone.selections.index'));

    assertDatabaseHas('selections', [
        'id' => $selection->id,
        'title' => 'Updated trip',
        'search_type' => 'long_term',
        'status' => 'archived',
    ]);
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
