<?php

use App\Models\Accommodation;
use App\Models\Criterion;
use App\Models\Selection;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\assertModelMissing;
use function Pest\Laravel\delete;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

test('a member can delete a criterion and only its answers are removed', function () {
    $user = User::factory()->create();
    $selection = Selection::factory()->create();
    $selection->users()->attach($user);
    $criterion = Criterion::factory()->for($selection)->create();
    $otherCriterion = Criterion::factory()->for($selection)->create();
    $accommodation = Accommodation::factory()->for($selection)->create();
    $accommodation->criteria()->attach([$criterion->id, $otherCriterion->id], ['is_met' => true]);
    actingAs($user);

    delete(route('userzone.selections.criteria.destroy', [$criterion->selection_id, $criterion]))
        ->assertRedirect(route('userzone.selections.criteria.index', $selection));

    assertModelMissing($criterion);
    assertDatabaseMissing('accommodation_criterion', ['criterion_id' => $criterion->id]);
    assertDatabaseHas('accommodation_criterion', ['criterion_id' => $otherCriterion->id, 'is_met' => true]);
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
