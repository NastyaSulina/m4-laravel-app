<?php

use App\Models\Criterion;
use App\Models\Selection;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\assertModelMissing;
use function Pest\Laravel\delete;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

test('a user can delete a criterion and return to its criteria list', function () {
    $user = User::factory()->create();
    $selection = Selection::factory()->for($user, 'owner')->create();
    $criterion = Criterion::factory()->for($selection)->create();
    $otherCriterion = Criterion::factory()->for($selection)->create();
    actingAs($user);

    delete(route('userzone.selections.criteria.destroy', [$criterion->selection_id, $criterion]))
        ->assertRedirect(route('userzone.selections.criteria.index', $selection));

    assertModelMissing($criterion);
    assertModelExists($otherCriterion);
    assertModelExists($selection);
});

test('owners can create a criterion', function () {
    $selection = Selection::factory()->create();
    actingAs($selection->owner);

    post(route('userzone.selections.criteria.store', $selection), [
        'title' => 'Balcony',
    ])->assertSessionHasNoErrors()->assertRedirect(route('userzone.selections.criteria.index', $selection));

    $criterion = $selection->criteria()->sole();
    expect($criterion->title)->toBe('Balcony');
});

test('owners can update a criterion', function () {
    $criterion = Criterion::factory()->create();
    actingAs($criterion->selection->owner);

    put(route('userzone.selections.criteria.update', [$criterion->selection, $criterion]), [
        'title' => 'Parking',
    ])->assertSessionHasNoErrors()->assertRedirect(route('userzone.selections.criteria.index', $criterion->selection));

    expect($criterion->fresh()->title)->toBe('Parking');
});
