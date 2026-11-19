<?php

use App\Models\Accommodation;
use App\Models\Criterion;
use App\Models\Selection;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\assertModelMissing;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;

test('deleting a selection deletes its contents and preserves other selections', function () {
    $selection = Selection::factory()->create();
    $criterion = Criterion::factory()->for($selection)->create();
    $accommodation = Accommodation::factory()->for($selection)->create();
    $other = Selection::factory()->create();
    $otherCriterion = Criterion::factory()->for($other)->create();
    $otherAccommodation = Accommodation::factory()->for($other)->create();
    actingAs($selection->owner);

    get(route('userzone.selections.show', $selection))->assertOk()
        ->assertSee('action="'.route('userzone.selections.destroy', $selection).'"', false)
        ->assertSee('name="_method" value="DELETE"', false);
    delete(route('userzone.selections.destroy', $selection))->assertRedirect(route('userzone.selections.index'));

    assertModelMissing($selection);
    assertModelMissing($criterion);
    assertModelMissing($accommodation);
    assertModelExists($selection->owner);
    assertModelExists($other);
    assertModelExists($otherCriterion);
    assertModelExists($otherAccommodation);
});

test('guests cannot delete selections', function () {
    $selection = Selection::factory()->create();
    delete(route('userzone.selections.destroy', $selection))->assertRedirect(route('login'));
    assertModelExists($selection);
});

test('deleting a missing selection returns not found', function () {
    actingAs(User::factory()->create());
    delete(route('userzone.selections.destroy', 999))->assertNotFound();
});

test('users cannot delete another owners selection or its contents', function () {
    $selection = Selection::factory()->create();
    $criterion = Criterion::factory()->for($selection)->create();
    $accommodation = Accommodation::factory()->for($selection)->create();
    actingAs(User::factory()->create());

    delete(route('userzone.selections.destroy', $selection))->assertForbidden();

    assertModelExists($selection);
    assertModelExists($criterion);
    assertModelExists($accommodation);
});

test('deleting a selection removes all its photos and preserves other selections photos', function () {
    $disk = Storage::fake('public');
    config(['media-library.disk_name' => 'public']);
    $selection = Selection::factory()->create();
    $accommodations = Accommodation::factory()->count(2)->for($selection)->create();
    $photos = $accommodations->map(fn (Accommodation $accommodation) => $accommodation
        ->addMedia(UploadedFile::fake()->image('room.jpg'))->toMediaCollection('images'));
    $other = Accommodation::factory()->create();
    $otherPhoto = $other->addMedia(UploadedFile::fake()->image('other.jpg'))->toMediaCollection('images');
    foreach ($photos as $photo) {
        $disk->assertExists([$photo->getPathRelativeToRoot(), $photo->getPathRelativeToRoot('thumbnail')]);
    }
    actingAs($selection->owner);

    delete(route('userzone.selections.destroy', $selection))->assertRedirect(route('userzone.selections.index'));

    foreach ($photos as $photo) {
        assertModelMissing($photo);
        $disk->assertMissing([$photo->getPathRelativeToRoot(), $photo->getPathRelativeToRoot('thumbnail')]);
    }
    assertModelExists($otherPhoto);
    $disk->assertExists([$otherPhoto->getPathRelativeToRoot(), $otherPhoto->getPathRelativeToRoot('thumbnail')]);
});
