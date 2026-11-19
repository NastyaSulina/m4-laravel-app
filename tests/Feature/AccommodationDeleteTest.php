<?php

use App\Models\Accommodation;
use App\Models\Selection;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\assertModelMissing;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;

test('owner can delete accommodation and its image without deleting its selection or other accommodation', function () {
    $disk = Storage::fake('public');
    config(['media-library.disk_name' => 'public']);
    $accommodation = Accommodation::factory()->create();
    $selection = $accommodation->selection;
    $other = Accommodation::factory()->for($selection)->create();
    $media = $accommodation->addMedia(UploadedFile::fake()->image('room.jpg'))->toMediaCollection('images');
    $original = $media->getPathRelativeToRoot();
    $thumbnail = $media->getPathRelativeToRoot('thumbnail');
    actingAs($selection->owner);
    $parameters = [$selection, $accommodation];

    get(route('userzone.selections.accommodations.show', $parameters))->assertOk()
        ->assertSee('action="'.route('userzone.selections.accommodations.destroy', $parameters).'"', false)
        ->assertSee('name="_method" value="DELETE"', false);
    delete(route('userzone.selections.accommodations.destroy', $parameters))
        ->assertRedirect(route('userzone.selections.show', $selection));

    assertModelMissing($accommodation);
    assertModelMissing($media);
    assertModelExists($selection);
    assertModelExists($other);
    $disk->assertMissing([$original, $thumbnail]);
});

test('guests cannot delete accommodation', function () {
    $accommodation = Accommodation::factory()->create();

    delete(route('userzone.selections.accommodations.destroy', [$accommodation->selection_id, $accommodation]))
        ->assertRedirect(route('login'));

    assertModelExists($accommodation);
});

test('other users cannot delete accommodation', function () {
    $accommodation = Accommodation::factory()->create();
    actingAs(User::factory()->create());

    delete(route('userzone.selections.accommodations.destroy', [$accommodation->selection_id, $accommodation]))
        ->assertForbidden();

    assertModelExists($accommodation);
});

test('deleting accommodation through the wrong selection returns not found', function () {
    $selection = Selection::factory()->create();
    $accommodation = Accommodation::factory()->create();
    actingAs($selection->owner);

    delete(route('userzone.selections.accommodations.destroy', [$selection, $accommodation]))->assertNotFound();

    assertModelExists($accommodation);
});

test('deleting missing accommodation returns not found', function () {
    $selection = Selection::factory()->create();
    actingAs($selection->owner);

    delete(route('userzone.selections.accommodations.destroy', [$selection, 999]))->assertNotFound();
});
