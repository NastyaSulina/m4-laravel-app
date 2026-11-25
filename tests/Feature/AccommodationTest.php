<?php

use App\Models\Accommodation;
use App\Models\Selection;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\assertModelMissing;
use function Pest\Laravel\delete;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

test('owners can add accommodation with server assigned selection and author', function () {
    $selection = Selection::factory()->create();
    $otherSelection = Selection::factory()->create();
    actingAs($selection->owner);

    post(route('userzone.selections.accommodations.store', $selection), [
        'title' => 'Madrid apartment',
        'source_url' => 'https://example.com/listing/1',
        'address' => 'Calle Mayor 1',
        'property_type' => 'apartment',
        'status' => 'considering',
        'selection_id' => $otherSelection->id,
        'user_id' => $otherSelection->owner_id,
    ])->assertSessionHasNoErrors()->assertRedirect(route('userzone.selections.show', $selection));

    $accommodation = Accommodation::sole();
    expect($accommodation->selection_id)->toBe($selection->id);
    expect($accommodation->user_id)->toBe($selection->owner_id);
    expect($accommodation->title)->toBe('Madrid apartment');
    expect($accommodation->source_url)->toBe('https://example.com/listing/1');
    expect($accommodation->address)->toBe('Calle Mayor 1');
    expect($accommodation->status)->toBe('considering');
    expect($accommodation->property_type)->toBe('apartment');
    expect($accommodation->note)->toBeNull();
});

test('owners and shared members can update accommodation without changing its author or selection', function (bool $sharedMember) {
    $accommodation = Accommodation::factory()->create()->refresh();
    $selection = $accommodation->selection;
    $other = Selection::factory()->create();
    $actor = $selection->owner;
    if ($sharedMember) {
        $actor = User::factory()->create();
        $selection->users()->attach($actor);
    }
    actingAs($actor);

    put(route('userzone.selections.accommodations.update', [$selection, $accommodation]), [
        'title' => 'Updated apartment',
        'source_url' => 'https://example.com/updated',
        'address' => 'New address',
        'status' => 'selected',
        'property_type' => 'room',
        'note' => null,
        'user_id' => $other->owner_id,
        'selection_id' => $other->id,
    ])->assertSessionHasNoErrors()
        ->assertRedirect(route('userzone.selections.accommodations.show', [$selection, $accommodation]));

    $updated = $accommodation->fresh();
    expect($updated->title)->toBe('Updated apartment');
    expect($updated->source_url)->toBe('https://example.com/updated');
    expect($updated->address)->toBe('New address');
    expect($updated->status)->toBe('selected');
    expect($updated->property_type)->toBe('room');
    expect($updated->note)->toBeNull();
    expect($updated->user_id)->toBe($accommodation->user_id);
    expect($updated->selection_id)->toBe($selection->id);
})->with(['owner' => false, 'shared member' => true]);

test('owner can delete accommodation without deleting its selection or other accommodation', function () {
    $accommodation = Accommodation::factory()->create();
    $selection = $accommodation->selection;
    $other = Accommodation::factory()->for($selection)->create();
    actingAs($selection->owner);
    $parameters = [$selection, $accommodation];

    delete(route('userzone.selections.accommodations.destroy', $parameters))
        ->assertRedirect(route('userzone.selections.show', $selection));

    assertModelMissing($accommodation);
    assertModelExists($selection);
    assertModelExists($other);
});

test('invalid updates leave accommodation unchanged', function () {
    $accommodation = Accommodation::factory()->create(['title' => 'Original'])->refresh();
    actingAs($accommodation->selection->owner);

    put(route('userzone.selections.accommodations.update', [$accommodation->selection_id, $accommodation]), [
        'title' => 'Hi',
        'source_url' => 'invalid',
        'address' => 'New address',
        'status' => 'new',
        'note' => str_repeat('a', 5001),
    ])->assertSessionHasErrors(['title', 'source_url', 'status', 'note']);

    expect($accommodation->fresh()->getAttributes())->toBe($accommodation->getAttributes());
});
