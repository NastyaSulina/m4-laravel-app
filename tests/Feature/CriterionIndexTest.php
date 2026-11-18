<?php

use App\Models\Criterion;
use App\Models\Selection;

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
