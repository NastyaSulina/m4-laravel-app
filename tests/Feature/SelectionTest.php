<?php

use App\Models\Accommodation;
use App\Models\Criterion;
use App\Models\Selection;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\assertModelMissing;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

test('selection creation accepts status but ignores supplied ownership and id', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    actingAs($user);

    post(route('userzone.selections.store'), [
        'title' => 'Madrid trip',
        'search_type' => 'short_stay',
        'owner_id' => $otherUser->id,
        'id' => 999,
        'status' => 'active',
    ])->assertSessionHasNoErrors()->assertRedirect(route('userzone.selections.index'));

    $selection = $user->selections()->sole();
    expect($selection->id)->not->toBe(999);
    assertDatabaseHas('selections', [
        'id' => $selection->id,
        'owner_id' => $user->id,
        'title' => 'Madrid trip',
        'search_type' => 'short_stay',
        'status' => 'active',
        'start_date' => null,
        'end_date' => null,
    ]);
});

test('selection update accepts status but ignores protected fields and clears omitted dates', function () {
    $selection = Selection::factory()->create([
        'status' => 'active',
        'start_date' => '2027-06-01',
        'end_date' => '2027-06-10',
    ]);
    $otherUser = User::factory()->create();
    actingAs($selection->owner);

    put(route('userzone.selections.update', $selection), [
        'title' => 'Updated trip',
        'search_type' => 'long_term',
        'owner_id' => $otherUser->id,
        'id' => 999,
        'status' => 'archived',
    ])->assertSessionHasNoErrors()->assertRedirect(route('userzone.selections.show', $selection));

    assertDatabaseHas('selections', [
        'id' => $selection->id,
        'owner_id' => $selection->owner_id,
        'title' => 'Updated trip',
        'search_type' => 'long_term',
        'status' => 'archived',
        'start_date' => null,
        'end_date' => null,
    ]);
});

test('deleting a selection deletes its contents and preserves other selections', function () {
    $selection = Selection::factory()->create();
    $criterion = Criterion::factory()->for($selection)->create();
    $accommodation = Accommodation::factory()->for($selection)->create();
    $other = Selection::factory()->create();
    $otherCriterion = Criterion::factory()->for($other)->create();
    $otherAccommodation = Accommodation::factory()->for($other)->create();
    actingAs($selection->owner);

    get(route('userzone.selections.show', $selection))->assertOk()
        ->assertSee('action="'.route('userzone.selections.destroy', $selection).'"', false);

    delete(route('userzone.selections.destroy', $selection))->assertRedirect(route('userzone.selections.index'));

    assertModelMissing($selection);
    assertModelMissing($criterion);
    assertModelMissing($accommodation);
    assertModelExists($selection->owner);
    assertModelExists($other);
    assertModelExists($otherCriterion);
    assertModelExists($otherAccommodation);
});

test('selection update rejects reversed dates without saving changes', function () {
    $selection = Selection::factory()->create(['title' => 'Original', 'start_date' => null, 'end_date' => null]);
    actingAs($selection->owner);

    put(route('userzone.selections.update', $selection), [
        'title' => 'Changed',
        'search_type' => 'short_stay',
        'start_date' => '2027-06-10',
        'end_date' => '2027-06-09',
    ])->assertSessionHasErrors('end_date');

    expect($selection->fresh()->title)->toBe('Original');
    expect($selection->fresh()->start_date)->toBeNull();
    expect($selection->fresh()->end_date)->toBeNull();
});

test('selection filters show only the appropriate accessible selections without duplicates', function (?string $filter, array $expectedTitles) {
    $user = User::factory()->create();
    $owned = Selection::factory()->for($user, 'owner')->create(['title' => 'Owned selection', 'created_at' => '2026-01-01']);
    $shared = Selection::factory()->create(['title' => 'Shared selection', 'created_at' => '2026-01-02']);
    $owned->users()->attach($user);
    $shared->users()->attach($user);
    Selection::factory()->create(['title' => 'Unrelated selection']);
    actingAs($user);

    $response = get(route('userzone.selections.index', $filter === null ? [] : ['filter' => $filter]));

    $response->assertOk();
    expect($response['selections']->pluck('title')->all())->toBe($expectedTitles);
    $response->assertViewHas('filter', $filter ?? 'my');
})->with([
    'default' => [null, ['Owned selection']],
    'shared' => ['shared', ['Shared selection']],
]);

test('a selection displays its accommodations and criteria without mixing in another selections contents', function () {
    $selection = Selection::factory()->create(['title' => 'Madrid shortlist']);
    $accommodation = Accommodation::factory()->for($selection)->create(['title' => 'Apartment near Retiro']);
    Criterion::factory()->for($selection)->create(['title' => 'Walking distance to the park']);
    $other = Selection::factory()->for($selection->owner, 'owner')->create();
    Accommodation::factory()->for($other)->create(['title' => 'Barcelona studio']);
    Criterion::factory()->for($other)->create(['title' => 'Sea view']);
    actingAs($selection->owner);

    get(route('userzone.selections.show', $selection))->assertOk()
        ->assertSeeText('Madrid shortlist')
        ->assertSeeText('Apartment near Retiro')
        ->assertSeeText('Walking distance to the park')
        ->assertSee(route('userzone.selections.accommodations.show', [$selection, $accommodation]))
        ->assertDontSeeText('Barcelona studio')
        ->assertDontSeeText('Sea view');
});

test('shared members cannot delete a selection', function () {
    $selection = Selection::factory()->create();
    $member = User::factory()->create();
    $selection->users()->attach($member);
    $accommodation = Accommodation::factory()->for($selection)->create();
    actingAs($member);

    get(route('userzone.selections.show', $selection))->assertOk()
        ->assertSee(route('userzone.selections.edit', $selection))
        ->assertDontSee('action="'.route('userzone.selections.destroy', $selection).'"', false);
    delete(route('userzone.selections.destroy', $selection))->assertForbidden();

    assertModelExists($selection);
    assertModelExists($accommodation);
});
