<?php

use App\Models\Accommodation;
use App\Models\Selection;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('owners can open the accommodation creation form', function () {
    $selection = Selection::factory()->create();
    actingAs($selection->owner);

    get(route('userzone.selections.accommodations.create', $selection))
        ->assertOk()
        ->assertSee('action="'.route('userzone.selections.accommodations.store', $selection).'"', false)
        ->assertSee('name="_token"', false)
        ->assertSee('name="title"', false)
        ->assertSee('name="source_url"', false)
        ->assertSee('name="address"', false)
        ->assertSee('name="status"', false)
        ->assertSee('name="property_type"', false)
        ->assertSee('name="note"', false)
        ->assertSee('maxlength="5000"', false)
        ->assertSeeText('Add accommodation');
});

test('accommodation form preserves notes and shows validation errors', function () {
    $selection = Selection::factory()->create();
    actingAs($selection->owner);
    $url = route('userzone.selections.accommodations.create', $selection);
    $note = '<script>alert(1)</script>';

    post(route('userzone.selections.accommodations.store', $selection), [
        'title' => 'Hi',
        'source_url' => 'https://example.com/listing/1',
        'address' => 'Calle Mayor 1',
        'status' => 'considering',
        'note' => $note,
    ])->assertSessionHasErrors('title');

    get($url)->assertOk()
        ->assertSee($note)
        ->assertDontSee($note, false)
        ->assertSeeText(__('validation.min.string', ['attribute' => 'title', 'min' => 3]));
});

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
    assertDatabaseCount('media', 0);
})->with([
    'optional fields omitted' => [[]],
    'optional fields supplied' => [['property_type' => 'apartment', 'note' => 'Near the station']],
    'maximum note length' => [['note' => str_repeat('a', 5000)]],
    'no image' => [['image' => null]],
]);

test('owners can attach an image when creating accommodation', function () {
    $disk = Storage::fake('public');
    config(['media-library.disk_name' => 'public']);
    $selection = Selection::factory()->create();
    actingAs($selection->owner);

    post(route('userzone.selections.accommodations.store', $selection), [
        'title' => 'Madrid apartment',
        'source_url' => 'https://example.com/listing/1',
        'address' => 'Calle Mayor 1',
        'status' => 'considering',
        'image' => UploadedFile::fake()->image('apartment.jpg')->size(3072),
    ])->assertSessionHasNoErrors()->assertRedirect(route('userzone.selections.show', $selection));

    $media = Accommodation::sole()->getMedia('images')->sole();
    expect($media->file_name)->toBe('apartment.jpg');
    $disk->assertExists($media->getPathRelativeToRoot());
});

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
    assertDatabaseCount('media', 0);
})->with([
    'short title' => [['title' => 'Hi'], 'title'],
    'invalid URL' => [['source_url' => 'not a URL'], 'source_url'],
    'unsafe URL' => [['source_url' => 'javascript:alert(1)'], 'source_url'],
    'missing address' => [['address' => ''], 'address'],
    'unknown status' => [['status' => 'new'], 'status'],
    'unknown property type' => [['property_type' => 'unknown'], 'property_type'],
    'invalid note' => [['note' => ['text']], 'note'],
    'note too long' => [['note' => str_repeat('a', 5001)], 'note'],
    'not an image' => fn () => [['image' => UploadedFile::fake()->create('document.pdf', 10, 'application/pdf')], 'image'],
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
