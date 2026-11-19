<?php

use App\Models\Criterion;
use App\Models\Selection;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\assertModelMissing;
use function Pest\Laravel\delete;

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

test('a guest cannot delete a criterion', function () {
    $criterion = Criterion::factory()->create();

    delete(route('userzone.selections.criteria.destroy', [$criterion->selection_id, $criterion]))
        ->assertRedirect(route('login'));

    assertModelExists($criterion);
});

test('deleting a missing criterion returns not found', function () {
    actingAs(User::factory()->create());

    delete(route('userzone.selections.criteria.destroy', [Selection::factory()->create(), 999]))->assertNotFound();
});

test('a user cannot delete another owners criterion', function () {
    $criterion = Criterion::factory()->create();
    actingAs(User::factory()->create());

    delete(route('userzone.selections.criteria.destroy', [$criterion->selection_id, $criterion]))
        ->assertForbidden();

    assertModelExists($criterion);
});
