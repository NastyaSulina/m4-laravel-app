<?php

use App\Models\Accommodation;
use App\Models\Criterion;
use App\Models\Selection;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\put;

test('outsiders cannot view a selection', function () {
    $user = User::factory()->create();
    $selection = Selection::factory()->create();
    actingAs($user);

    get(route('userzone.selections.show', $selection))->assertForbidden();
});

test('users cannot update another owners selection', function () {
    $selection = Selection::factory()->create(['title' => 'Original selection']);
    actingAs(User::factory()->create());

    put(route('userzone.selections.update', $selection), [
        'title' => 'Changed selection',
        'search_type' => 'short_stay',
    ])->assertForbidden();

    expect($selection->fresh()->title)->toBe('Original selection');
});

test('nested routes reject records from another selection', function () {
    $user = User::factory()->create();
    $selection = Selection::factory()->for($user, 'owner')->create();
    $otherSelection = Selection::factory()->for($user, 'owner')->create();
    $criterion = Criterion::factory()->for($otherSelection)->create(['title' => 'Original']);
    $accommodation = Accommodation::factory()->for($otherSelection)->create();
    actingAs($user);

    get(route('userzone.selections.accommodations.show', [$selection, $accommodation]))->assertNotFound();
    \Pest\Laravel\delete(route('userzone.selections.criteria.destroy', [$selection, $criterion]))->assertNotFound();

    expect($criterion->fresh()->title)->toBe('Original');
});
