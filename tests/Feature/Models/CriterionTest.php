<?php

use App\Models\Criterion;
use App\Models\Selection;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertModelExists;

test('factory creates a criterion belonging to a selection', function () {
    $criterion = Criterion::factory()->create();

    assertModelExists($criterion);
    assertModelExists($criterion->selection);
    expect($criterion->selection->criteria->sole()->id)->toBe($criterion->id);
});

test('criteria can use an existing selection and store whether they are required', function (bool $isRequired) {
    $selection = Selection::factory()->create();

    $criterion = Criterion::factory()->for($selection)->create(['is_required' => $isRequired]);

    assertDatabaseCount('selections', 1);
    assertDatabaseHas('criteria', [
        'id' => $criterion->id,
        'selection_id' => $selection->id,
        'is_required' => $isRequired,
    ]);
})->with([true, false]);

test('criterion is optional by default', function () {
    $criterion = new Criterion;
    $criterion->selection()->associate(Selection::factory()->create());
    $criterion->title = 'Balcony';

    $criterion->save();

    assertDatabaseHas('criteria', ['id' => $criterion->id, 'is_required' => false]);
});
