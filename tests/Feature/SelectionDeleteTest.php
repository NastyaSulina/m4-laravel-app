<?php

use App\Models\Accommodation;
use App\Models\Criterion;
use App\Models\Selection;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\assertModelMissing;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;

test('deleting a selection deletes its contents and preserves other selections', function () {
    $selection = Selection::factory()->create();
    $criterion = Criterion::factory()->for($selection)->create();
    $accommodation = Accommodation::factory()->for($selection)->create();
    $other = Selection::factory()->create();
    $otherCriterion = Criterion::factory()->for($other)->create();
    $otherAccommodation = Accommodation::factory()->for($other)->create();
    actingAs($selection->owner);

    get(route('userzone.selections.show', $selection))->assertOk()
        ->assertSee('action="'.route('userzone.selections.destroy', $selection).'"', false)
        ->assertSee('name="_method" value="DELETE"', false);
    delete(route('userzone.selections.destroy', $selection))->assertRedirect(route('userzone.selections.index'));

    assertModelMissing($selection);
    assertModelMissing($criterion);
    assertModelMissing($accommodation);
    assertModelExists($selection->owner);
    assertModelExists($other);
    assertModelExists($otherCriterion);
    assertModelExists($otherAccommodation);
});

test('guests cannot delete selections', function () {
    $selection = Selection::factory()->create();
    delete(route('userzone.selections.destroy', $selection))->assertRedirect(route('login'));
    assertModelExists($selection);
});

test('deleting a missing selection returns not found', function () {
    actingAs(User::factory()->create());
    delete(route('userzone.selections.destroy', 999))->assertNotFound();
});
