<?php

use App\Models\Accommodation;
use App\Models\Selection;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

it('accepts the three accommodation statuses on create and update', function (string $status) {
    $selection = Selection::factory()->create();
    actingAs($selection->owner);
    Accommodation::factory()->for($selection)->create(['status' => 'selected']);
    $data = [
        'title' => 'Another apartment',
        'source_url' => 'https://example.com/place',
        'address' => 'Madrid',
        'property_type' => 'apartment',
        'status' => $status,
    ];

    post(route('userzone.selections.accommodations.store', $selection), $data)->assertSessionHasNoErrors()->assertRedirect();
    $accommodation = $selection->accommodations()->latest('id')->first();
    expect($accommodation->status)->toBe($status);

    put(route('userzone.selections.accommodations.update', [$selection, $accommodation]), $data)
        ->assertSessionHasNoErrors()->assertRedirect();
    expect($accommodation->fresh()->status)->toBe($status);
    if ($status === 'selected') {
        expect($selection->accommodations()->where('status', 'selected')->count())->toBe(2);
    }
})->with(['considering', 'selected', 'rejected']);

it('rejects removed statuses on create and update', function (string $status) {
    $accommodation = Accommodation::factory()->create(['status' => 'considering']);
    $selection = $accommodation->selection;
    actingAs($selection->owner);
    $data = [
        'title' => 'Another apartment',
        'source_url' => 'https://example.com/place',
        'address' => 'Madrid',
        'property_type' => 'apartment',
        'status' => $status,
    ];

    post(route('userzone.selections.accommodations.store', $selection), $data)->assertSessionHasErrors('status');
    put(route('userzone.selections.accommodations.update', [$selection, $accommodation]), $data)->assertSessionHasErrors('status');
    expect($selection->accommodations()->count())->toBe(1);
    expect($accommodation->fresh()->status)->toBe('considering');
})->with(['shortlisted', 'unavailable']);
