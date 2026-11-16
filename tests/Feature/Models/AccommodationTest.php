<?php

use App\Models\Accommodation;

use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\assertDatabaseCount;

test('factory creates an accommodation belonging to a selection', function () {
    $accommodation = Accommodation::factory()->create();

    assertModelExists($accommodation);
    assertModelExists($accommodation->selection);
    expect($accommodation->selection->accommodations->sole()->id)->toBe($accommodation->id);
});

test('accommodation can be saved with only required fields', function () {
    $selection = \App\Models\Selection::factory()->create();
    $accommodation = new Accommodation;
    $accommodation->selection()->associate($selection);
    $accommodation->title = 'Apartment in Madrid';
    $accommodation->source_url = 'https://example.com/listing/1';
    $accommodation->address = 'Calle Mayor 1, Madrid';

    $accommodation->save();
    $accommodation->refresh();

    expect($accommodation->status)->toBe('considering');
    expect($accommodation->property_type)->toBeNull();
    expect($accommodation->note)->toBeNull();
});

test('factory can attach an accommodation to an existing selection', function () {
    $selection = \App\Models\Selection::factory()->create();

    $accommodation = Accommodation::factory()->for($selection)->create();

    expect($accommodation->selection_id)->toBe($selection->id);
    assertDatabaseCount('selections', 1);
});
