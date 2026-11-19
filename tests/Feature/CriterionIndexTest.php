<?php

use App\Models\Criterion;
use App\Models\Selection;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

test('criteria management displays only the selections criteria and its forms', function () {
    $selection = Selection::factory()->create();
    $criterion = Criterion::factory()->for($selection)->create(['title' => 'Wi-Fi']);
    Criterion::factory()->create(['title' => 'Unrelated criterion']);
    actingAs($selection->owner);

    get(route('userzone.selections.criteria.index', $selection))
        ->assertOk()
        ->assertSeeText($selection->title)
        ->assertSeeText('Wi-Fi')
        ->assertDontSeeText('Unrelated criterion')
        ->assertSee(route('userzone.selections.criteria.edit', [$selection, $criterion]))
        ->assertSee(route('userzone.selections.criteria.destroy', [$selection, $criterion]))
        ->assertSee(route('userzone.selections.criteria.store', $selection))
        ->assertSeeText('Add criterion');
});

test('empty criteria lists still offer creation', function () {
    $selection = Selection::factory()->create();
    actingAs($selection->owner);

    get(route('userzone.selections.criteria.index', $selection))
        ->assertOk()->assertSeeText('No criteria yet.')->assertSeeText('Add criterion');
});

test('criterion creation and update return to management', function () {
    $selection = Selection::factory()->create();
    actingAs($selection->owner);
    $index = route('userzone.selections.criteria.index', $selection);

    post(route('userzone.selections.criteria.store', $selection), ['title' => 'Balcony'])
        ->assertSessionHasNoErrors()->assertRedirect($index);
    $criterion = $selection->criteria()->sole();
    expect($criterion->title)->toBe('Balcony');

    get(route('userzone.selections.criteria.edit', [$selection, $criterion]))
        ->assertOk()->assertSee($index);
    put(route('userzone.selections.criteria.update', [$selection, $criterion]), ['title' => 'Parking', 'is_required' => 1])
        ->assertSessionHasNoErrors()->assertRedirect($index);
    expect($criterion->fresh()->title)->toBe('Parking');
});

test('guests cannot open criteria management', function () {
    get(route('userzone.selections.criteria.index', 1))->assertRedirect(route('login'));
});

test('a user cannot view another owners criteria management', function () {
    $selection = Selection::factory()->create();
    actingAs(User::factory()->create());

    get(route('userzone.selections.criteria.index', $selection))->assertForbidden();
});

test('a user cannot open another owners criterion edit form', function () {
    $criterion = Criterion::factory()->create();
    actingAs(User::factory()->create());

    get(route('userzone.selections.criteria.edit', [$criterion->selection_id, $criterion]))
        ->assertForbidden();
});

test('a user cannot add a criterion to another owners selection', function () {
    $selection = Selection::factory()->create();
    actingAs(User::factory()->create());

    post(route('userzone.selections.criteria.store', $selection), ['title' => 'Balcony'])
        ->assertForbidden();

    expect($selection->criteria()->count())->toBe(0);
});

test('a user cannot update another owners criterion', function () {
    $criterion = Criterion::factory()->create(['title' => 'Balcony', 'is_required' => false]);
    actingAs(User::factory()->create());

    put(route('userzone.selections.criteria.update', [$criterion->selection_id, $criterion]), [
        'title' => 'Parking',
        'is_required' => 1,
    ])->assertForbidden();

    expect($criterion->fresh()->title)->toBe('Balcony');
    expect((bool) $criterion->fresh()->is_required)->toBeFalse();
});
