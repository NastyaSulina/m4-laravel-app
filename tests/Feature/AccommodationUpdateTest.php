<?php

use App\Models\Accommodation;
use App\Models\Selection;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\put;

test('accommodation update requires a property type without changing existing data', function (array $input) {
    $accommodation = Accommodation::factory()->create(['title' => 'Original', 'property_type' => 'house']);
    actingAs($accommodation->selection->owner);

    put(route('userzone.selections.accommodations.update', [$accommodation->selection_id, $accommodation]), [
        'title' => 'Changed apartment',
        'source_url' => 'https://example.com/listing',
        'address' => 'New address',
        'status' => 'considering',
        ...$input,
    ])->assertSessionHasErrors('property_type');

    expect($accommodation->fresh()->title)->toBe('Original');
    expect($accommodation->fresh()->property_type)->toBe('house');
})->with([
    'missing' => [[]],
    'null' => [['property_type' => null]],
    'empty' => [['property_type' => '']],
]);

test('owners can open a prefilled accommodation edit form', function () {
    $accommodation = Accommodation::factory()->create([
        'title' => 'Madrid apartment',
        'source_url' => 'https://example.com/apartment',
        'address' => 'Calle Mayor 1',
        'status' => 'shortlisted',
        'property_type' => 'house',
        'note' => '<script>alert(1)</script>',
    ]);
    actingAs($accommodation->selection->owner);
    $parameters = [$accommodation->selection_id, $accommodation];

    get(route('userzone.selections.accommodations.edit', $parameters))
        ->assertOk()
        ->assertSee('action="'.route('userzone.selections.accommodations.update', $parameters).'"', false)
        ->assertSee('name="_method" value="PUT"', false)
        ->assertSee('enctype="multipart/form-data"', false)
        ->assertSee('value="Madrid apartment"', false)
        ->assertSee('value="https://example.com/apartment"', false)
        ->assertSee('value="Calle Mayor 1"', false)
        ->assertSee('value="shortlisted" selected', false)
        ->assertSee('value="house" selected', false)
        ->assertSee($accommodation->note)
        ->assertDontSee($accommodation->note, false)
        ->assertSeeText('Update accommodation');
});

test('accommodation edit form retains submitted values after validation failure', function () {
    $accommodation = Accommodation::factory()->create();
    actingAs($accommodation->selection->owner);
    $parameters = [$accommodation->selection_id, $accommodation];

    put(route('userzone.selections.accommodations.update', $parameters), [
        'title' => 'Hi',
        'source_url' => 'https://example.com/changed',
        'address' => 'Changed address',
        'status' => 'selected',
        'property_type' => 'hotel',
        'note' => 'Updated note',
    ])->assertSessionHasErrors('title');

    get(route('userzone.selections.accommodations.edit', $parameters))
        ->assertOk()
        ->assertSee('value="Hi"', false)
        ->assertSee('value="selected" selected', false)
        ->assertSee('value="hotel" selected', false)
        ->assertSeeText('Updated note')
        ->assertSeeText(__('validation.min.string', ['attribute' => 'title', 'min' => 3]));
});

test('owners can update accommodation while preserving its author and selection', function () {
    $accommodation = Accommodation::factory()->create()->refresh();
    $selection = $accommodation->selection;
    $other = Selection::factory()->create();
    actingAs($selection->owner);

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

test('other users cannot edit or update accommodation', function () {
    $accommodation = Accommodation::factory()->create()->refresh();
    actingAs(User::factory()->create());
    $parameters = [$accommodation->selection_id, $accommodation];

    get(route('userzone.selections.accommodations.edit', $parameters))->assertForbidden();
    put(route('userzone.selections.accommodations.update', $parameters), [])->assertForbidden();

    expect($accommodation->fresh()->getAttributes())->toBe($accommodation->getAttributes());
});

test('accommodation editing rejects a mismatched selection', function () {
    $selection = Selection::factory()->create();
    $accommodation = Accommodation::factory()->create()->refresh();
    actingAs($selection->owner);
    $parameters = [$selection, $accommodation];

    get(route('userzone.selections.accommodations.edit', $parameters))->assertNotFound();
    put(route('userzone.selections.accommodations.update', $parameters), [])->assertNotFound();
});

test('guests cannot edit or update accommodation', function () {
    $accommodation = Accommodation::factory()->create()->refresh();
    $parameters = [$accommodation->selection_id, $accommodation];

    get(route('userzone.selections.accommodations.edit', $parameters))->assertRedirect(route('login'));
    put(route('userzone.selections.accommodations.update', $parameters), [])->assertRedirect(route('login'));
});

test('updates keep the image unless a replacement is uploaded', function (bool $upload) {
    $disk = Storage::fake('public');
    config(['media-library.disk_name' => 'public']);
    $accommodation = Accommodation::factory()->create()->refresh();
    $existing = $accommodation->addMedia(UploadedFile::fake()->image('existing.jpg'))->toMediaCollection('images');
    actingAs($accommodation->selection->owner);
    get(route('userzone.selections.accommodations.edit', [$accommodation->selection_id, $accommodation]))
        ->assertOk()->assertSee('src="'.$existing->getUrl('thumbnail').'"', false);

    $input = [
        'title' => 'Updated apartment',
        'source_url' => 'https://example.com/updated',
        'address' => 'New address',
        'status' => 'considering',
        'property_type' => 'apartment',
    ];
    if ($upload) {
        $input['image'] = UploadedFile::fake()->image('new.jpg');
    }

    put(route('userzone.selections.accommodations.update', [$accommodation->selection_id, $accommodation]), $input)
        ->assertSessionHasNoErrors()->assertRedirect();

    $media = $accommodation->fresh()->getMedia('images');
    expect($media)->toHaveCount(1);
    if ($upload) {
        $disk->assertMissing($existing->getPathRelativeToRoot());
        expect($media->last()->file_name)->toBe('new.jpg');
        $disk->assertExists($media->last()->getPathRelativeToRoot());
    } else {
        expect($media->sole()->id)->toBe($existing->id);
        $disk->assertExists($existing->getPathRelativeToRoot());
    }
})->with([false, true]);

test('accommodation without an image displays the fallback', function () {
    $accommodation = Accommodation::factory()->create();
    $selection = $accommodation->selection;
    actingAs($selection->owner);

    foreach ([
        route('userzone.selections.accommodations.edit', [$selection, $accommodation]),
        route('userzone.selections.accommodations.show', [$selection, $accommodation]),
        route('userzone.selections.show', $selection),
    ] as $url) {
        get($url)->assertOk()->assertSee('src="/img/default-accommodation.png"', false);
    }
});
