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

test('accommodation creation requires a property type', function () {
    $selection = Selection::factory()->create();
    actingAs($selection->owner);

    post(route('userzone.selections.accommodations.store', $selection), [
        'title' => 'Madrid apartment',
        'source_url' => 'https://example.com/listing',
        'address' => 'Calle Mayor 1',
        'status' => 'considering',
    ])->assertSessionHasErrors('property_type');

    assertDatabaseCount('accommodations', 0);
});

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
        ->assertSee('enctype="multipart/form-data"', false)
        ->assertSee('type="file" name="image"', false)
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
        'property_type' => 'apartment',
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
        'property_type' => 'apartment',
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
    expect($accommodation->property_type)->toBe('apartment');
    expect($accommodation->note)->toBe($optional['note'] ?? null);
    assertDatabaseCount('media', 0);
})->with([
    'optional fields omitted' => [[]],
    'optional fields supplied' => [['note' => 'Near the station']],
    'maximum note length' => [['note' => str_repeat('a', 5000)]],
    'no image' => [['image' => null]],
]);

test('owners can attach JPG JPEG and PNG images up to ten MiB', function (string $extension) {
    $disk = Storage::fake('public');
    config(['media-library.disk_name' => 'public']);
    $selection = Selection::factory()->create();
    actingAs($selection->owner);

    post(route('userzone.selections.accommodations.store', $selection), [
        'title' => 'Madrid apartment',
        'source_url' => 'https://example.com/listing/1',
        'address' => 'Calle Mayor 1',
        'property_type' => 'apartment',
        'status' => 'considering',
        'image' => UploadedFile::fake()->image('apartment.'.$extension, 1200, 900)->size(10240),
    ])->assertSessionHasNoErrors()->assertRedirect(route('userzone.selections.show', $selection));

    $media = Accommodation::sole()->getMedia('images')->sole();
    expect($media->file_name)->toBe('apartment.'.$extension);
    $disk->assertExists($media->getPathRelativeToRoot());
    $disk->assertExists($media->getPathRelativeToRoot('thumbnail'));
    $thumbnailSize = getimagesize($media->getPath('thumbnail'));
    expect($thumbnailSize[0])->toBe(600);
    expect($thumbnailSize[1])->toBe(450);

    $accommodation = Accommodation::sole();
    get(route('userzone.selections.accommodations.show', [$selection, $accommodation]))
        ->assertOk()
        ->assertSee('src="'.$media->getUrl().'"', false);

    get(route('userzone.selections.show', $selection))
        ->assertOk()
        ->assertSee('src="'.$media->getUrl('thumbnail').'"', false);
})->with(['jpg', 'jpeg', 'png']);

test('invalid accommodation data is not saved', function (array $invalid, string $field) {
    $selection = Selection::factory()->create();
    actingAs($selection->owner);

    post(route('userzone.selections.accommodations.store', $selection), array_replace([
        'title' => 'Madrid apartment',
        'source_url' => 'https://example.com/listing/1',
        'address' => 'Calle Mayor 1',
        'property_type' => 'apartment',
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
    'empty property type' => [['property_type' => ''], 'property_type'],
    'null property type' => [['property_type' => null], 'property_type'],
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

test('invalid images return field errors and preserve form input without creating accommodation', function (UploadedFile $image, string $message) {
    $disk = Storage::fake('public');
    config(['media-library.disk_name' => 'public']);
    $selection = Selection::factory()->create();
    actingAs($selection->owner);
    $formUrl = route('userzone.selections.accommodations.create', $selection);
    $input = [
        'title' => 'Madrid apartment',
        'source_url' => 'https://example.com/listing',
        'address' => 'Calle Mayor 1',
        'status' => 'considering',
        'property_type' => 'apartment',
        'note' => 'Near the station',
    ];

    $response = $this->from($formUrl)->post(route('userzone.selections.accommodations.store', $selection), [
        ...$input,
        'image' => $image,
    ])->assertRedirect($formUrl)->assertSessionHasErrors(['image' => $message]);

    foreach ($input as $field => $value) {
        $response->assertSessionHasInput($field, $value);
    }
    assertDatabaseCount('accommodations', 0);
    assertDatabaseCount('media', 0);
    $disk->assertDirectoryEmpty('/');
    get($formUrl)->assertOk()->assertSeeText($message)->assertSeeText('Near the station');
})->with([
    'unsupported format' => fn () => [UploadedFile::fake()->image('room.gif'), __('validation.mimes', ['attribute' => 'image', 'values' => 'jpg, jpeg, png'])],
    'too large' => fn () => [UploadedFile::fake()->image('room.jpg')->size(10241), __('validation.max.file', ['attribute' => 'image', 'max' => 10240])],
]);
