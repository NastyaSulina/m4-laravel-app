<?php

use App\Models\Accommodation;
use App\Models\Selection;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertModelExists;

test('factory creates an accommodation belonging to a selection', function () {
    $accommodation = Accommodation::factory()->create();

    assertModelExists($accommodation);
    assertModelExists($accommodation->selection);
    assertModelExists($accommodation->user);
    expect($accommodation->user->accommodations->sole()->id)->toBe($accommodation->id);
    expect($accommodation->selection->accommodations->sole()->id)->toBe($accommodation->id);
});

test('accommodation can be saved with only required fields', function () {
    $selection = Selection::factory()->create();
    $accommodation = new Accommodation;
    $accommodation->selection()->associate($selection);
    $accommodation->user()->associate($selection->owner);
    $accommodation->title = 'Apartment in Madrid';
    $accommodation->source_url = 'https://example.com/listing/1';
    $accommodation->address = 'Calle Mayor 1, Madrid';
    $accommodation->property_type = 'apartment';

    $accommodation->save();
    $accommodation->refresh();

    expect($accommodation->status)->toBe('considering');
    expect($accommodation->property_type)->toBe('apartment');
    expect($accommodation->note)->toBeNull();
});

test('factory can attach an accommodation to an existing selection', function () {
    $selection = Selection::factory()->create();

    $accommodation = Accommodation::factory()->for($selection)->for($selection->owner)->create();

    expect($accommodation->selection_id)->toBe($selection->id);
    expect($accommodation->user_id)->toBe($selection->owner_id);
    assertDatabaseCount('users', 1);
    assertDatabaseCount('selections', 1);
});
