<?php

use App\Models\Accommodation;
use App\Models\Selection;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('owners can add accommodation with server assigned selection and author', function (array $optional) {
    $selection = Selection::factory()->create();
    $otherSelection = Selection::factory()->create();
    actingAs($selection->owner);

    post(route('userzone.selections.accommodations.store', $selection), [
        'title' => 'Madrid apartment',
        'source_url' => 'https://example.com/listing/1',
        'address' => 'Calle Mayor 1',
        'status' => 'shortlisted',
        'selection_id' => $otherSelection->id,
        'user_id' => $otherSelection->owner_id,
        ...$optional,
    ])->assertSessionHasNoErrors()->assertRedirect(route('userzone.selections.show', $selection));

    $accommodation = Accommodation::sole();
    expect($accommodation->selection_id)->toBe($selection->id);
    expect($accommodation->user_id)->toBe($selection->owner_id);
    expect($accommodation->title)->toBe('Madrid apartment');
    expect($accommodation->source_url)->toBe('https://example.com/listing/1');
    expect($accommodation->address)->toBe('Calle Mayor 1');
    expect($accommodation->status)->toBe('shortlisted');
    expect($accommodation->property_type)->toBe($optional['property_type'] ?? null);
    expect($accommodation->note)->toBe($optional['note'] ?? null);
})->with([
    'optional fields omitted' => [[]],
    'optional fields supplied' => [['property_type' => 'apartment', 'note' => 'Near the station']],
    'maximum note length' => [['note' => str_repeat('a', 5000)]],
]);

test('invalid accommodation data is not saved', function (array $invalid, string $field) {
    $selection = Selection::factory()->create();
    actingAs($selection->owner);

    post(route('userzone.selections.accommodations.store', $selection), array_replace([
        'title' => 'Madrid apartment',
        'source_url' => 'https://example.com/listing/1',
        'address' => 'Calle Mayor 1',
        'status' => 'considering',
    ], $invalid))->assertSessionHasErrors($field);

    assertDatabaseCount('accommodations', 0);
})->with([
    'short title' => [['title' => 'Hi'], 'title'],
    'invalid URL' => [['source_url' => 'not a URL'], 'source_url'],
    'unsafe URL' => [['source_url' => 'javascript:alert(1)'], 'source_url'],
    'missing address' => [['address' => ''], 'address'],
    'unknown status' => [['status' => 'new'], 'status'],
    'unknown property type' => [['property_type' => 'unknown'], 'property_type'],
    'invalid note' => [['note' => ['text']], 'note'],
    'note too long' => [['note' => str_repeat('a', 5001)], 'note'],
]);

test('other users cannot create accommodations in a selection', function () {
    $selection = Selection::factory()->create();
    actingAs(User::factory()->create());

    get(route('userzone.selections.accommodations.create', $selection))->assertForbidden();
    post(route('userzone.selections.accommodations.store', $selection), [])->assertForbidden();

    assertDatabaseCount('accommodations', 0);
});

test('guests cannot create accommodations', function () {
    $selection = Selection::factory()->create();

    get(route('userzone.selections.accommodations.create', $selection))->assertRedirect(route('login'));
    post(route('userzone.selections.accommodations.store', $selection), [])->assertRedirect(route('login'));

    assertDatabaseCount('accommodations', 0);
});
