<?php

use App\Models\Accommodation;
use App\Models\Criterion;
use App\Models\Selection;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\get;
use function Pest\Laravel\put;

test('criteria form shows saved answers and new criteria as unchecked', function () {
    $accommodation = Accommodation::factory()->create();
    $selection = $accommodation->selection;
    $criterion = Criterion::factory()->for($selection)->create(['title' => 'Parking']);
    $accommodation->criteria()->attach($criterion, ['is_met' => false]);
    Criterion::factory()->for($selection)->create(['title' => 'European breakfast']);
    Criterion::factory()->create(['title' => 'Unrelated requirement']);
    actingAs($selection->owner);

    get(route('userzone.selections.accommodations.criteria.edit', [$selection, $accommodation]))->assertOk()
        ->assertSeeText('Parking')->assertSeeText('European breakfast')
        ->assertSee('value="0" selected', false)
        ->assertSee('value="" selected', false)
        ->assertDontSeeText('Unrelated requirement');
});

test('selection cards display saved results and newly added criteria as not checked', function () {
    $accommodation = Accommodation::factory()->create();
    $selection = $accommodation->selection;
    $parking = Criterion::factory()->for($selection)->create(['title' => 'Parking']);
    $quietArea = Criterion::factory()->for($selection)->create(['title' => 'Quiet area']);
    $breakfast = Criterion::factory()->for($selection)->create(['title' => 'Breakfast']);
    $accommodation->criteria()->attach([
        $parking->id => ['is_met' => true],
        $quietArea->id => ['is_met' => false],
        $breakfast->id => ['is_met' => null],
    ]);
    Criterion::factory()->for($selection)->create(['title' => 'Kitchen']);
    actingAs($selection->owner);

    get(route('userzone.selections.show', $selection))->assertOk()
        ->assertSeeInOrder(['Parking', 'Met', 'Quiet area', 'Not met', 'Breakfast', 'Not checked', 'Kitchen', 'Not checked']);
});

test('owners and members can save all three criterion results without changing other accommodation', function (bool $sharedMember) {
    $accommodation = Accommodation::factory()->create();
    $selection = $accommodation->selection;
    $criteria = Criterion::factory(3)->for($selection)->create();
    $otherAccommodation = Accommodation::factory()->for($selection)->create();
    $accommodation->criteria()->attach($criteria[2], ['is_met' => true]);
    $otherAccommodation->criteria()->attach($criteria[0], ['is_met' => false]);
    $actor = $selection->owner;
    if ($sharedMember) {
        $actor = User::factory()->create();
        $selection->users()->attach($actor);
    }
    actingAs($actor);

    put(route('userzone.selections.accommodations.criteria.update', [$selection, $accommodation]), [
        'criteria' => [
            ['id' => $criteria[0]->id, 'is_met' => '1'],
            ['id' => $criteria[1]->id, 'is_met' => '0'],
            ['id' => $criteria[2]->id, 'is_met' => ''],
        ],
    ])->assertSessionHasNoErrors()
        ->assertRedirect(route('userzone.selections.accommodations.show', [$selection, $accommodation]));

    assertDatabaseHas('accommodation_criterion', ['accommodation_id' => $accommodation->id, 'criterion_id' => $criteria[0]->id, 'is_met' => true]);
    assertDatabaseHas('accommodation_criterion', ['accommodation_id' => $accommodation->id, 'criterion_id' => $criteria[1]->id, 'is_met' => false]);
    assertDatabaseHas('accommodation_criterion', ['accommodation_id' => $accommodation->id, 'criterion_id' => $criteria[2]->id, 'is_met' => null]);
    assertDatabaseHas('accommodation_criterion', ['accommodation_id' => $otherAccommodation->id, 'criterion_id' => $criteria[0]->id, 'is_met' => false]);
})->with(['owner' => false, 'shared member' => true]);

test('criteria from another selection are rejected without saving any changes', function () {
    $accommodation = Accommodation::factory()->create();
    $criterion = Criterion::factory()->for($accommodation->selection)->create();
    $foreignCriterion = Criterion::factory()->create();
    $accommodation->criteria()->attach($criterion, ['is_met' => true]);
    actingAs($accommodation->selection->owner);

    put(route('userzone.selections.accommodations.criteria.update', [$accommodation->selection, $accommodation]), [
        'criteria' => [
            ['id' => $criterion->id, 'is_met' => '0'],
            ['id' => $foreignCriterion->id, 'is_met' => '1'],
        ],
    ])->assertSessionHasErrors('criteria.1.id');

    assertDatabaseHas('accommodation_criterion', ['accommodation_id' => $accommodation->id, 'criterion_id' => $criterion->id, 'is_met' => true]);
    assertDatabaseMissing('accommodation_criterion', ['accommodation_id' => $accommodation->id, 'criterion_id' => $foreignCriterion->id]);
});

test('invalid results show one general error and preserve other answers', function () {
    $accommodation = Accommodation::factory()->create();
    $selection = $accommodation->selection;
    $criteria = Criterion::factory(2)->for($selection)->create();
    $accommodation->criteria()->attach($criteria[0], ['is_met' => true]);
    actingAs($selection->owner);
    $editUrl = route('userzone.selections.accommodations.criteria.edit', [$selection, $accommodation]);

    $this->from($editUrl)->put(route('userzone.selections.accommodations.criteria.update', [$selection, $accommodation]), [
        'criteria' => [
            ['id' => $criteria[0]->id, 'is_met' => '0'],
            ['id' => $criteria[1]->id, 'is_met' => 'maybe'],
        ],
    ])->assertRedirect($editUrl)->assertSessionHasErrors('criteria.1.is_met');

    assertDatabaseHas('accommodation_criterion', ['accommodation_id' => $accommodation->id, 'criterion_id' => $criteria[0]->id, 'is_met' => true]);
    get($editUrl)->assertOk()
        ->assertSee('value="0" selected', false)
        ->assertSeeText('Some criteria or answers are invalid. Reload the page and try again.')
        ->assertDontSeeText('criteria.1');
});

test('outsiders cannot change accommodation criteria', function () {
    $accommodation = Accommodation::factory()->create();
    $criterion = Criterion::factory()->for($accommodation->selection)->create();
    actingAs(User::factory()->create());

    get(route('userzone.selections.accommodations.criteria.edit', [$accommodation->selection, $accommodation]))->assertForbidden();

    put(route('userzone.selections.accommodations.criteria.update', [$accommodation->selection, $accommodation]), [
        'criteria' => [['id' => $criterion->id, 'is_met' => '1']],
    ])->assertForbidden();

    assertDatabaseMissing('accommodation_criterion', ['accommodation_id' => $accommodation->id]);
});

test('criterion results cannot be saved through another selections accommodation route', function () {
    $selection = Selection::factory()->create();
    $otherSelection = Selection::factory()->for($selection->owner, 'owner')->create();
    $accommodation = Accommodation::factory()->for($otherSelection)->create();
    $criterion = Criterion::factory()->for($selection)->create();
    actingAs($selection->owner);

    get(route('userzone.selections.accommodations.criteria.edit', [$selection, $accommodation]))->assertNotFound();

    put(route('userzone.selections.accommodations.criteria.update', [$selection, $accommodation]), [
        'criteria' => [['id' => $criterion->id, 'is_met' => '1']],
    ])->assertNotFound();

    assertDatabaseMissing('accommodation_criterion', ['accommodation_id' => $accommodation->id]);
});
