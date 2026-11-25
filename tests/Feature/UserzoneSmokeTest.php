<?php

use App\Models\Accommodation;
use App\Models\Criterion;
use App\Models\Selection;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

dataset('userzone pages', [
    'dashboard' => ['dashboard', []],
    'profile' => ['profile.edit', []],
    'selections' => ['userzone.selections.index', []],
    'new selection' => ['userzone.selections.create', []],
    'selection' => ['userzone.selections.show', ['selection']],
    'edit selection' => ['userzone.selections.edit', ['selection']],
    'new accommodation' => ['userzone.selections.accommodations.create', ['selection']],
    'accommodation' => ['userzone.selections.accommodations.show', ['selection', 'accommodation']],
    'edit accommodation' => ['userzone.selections.accommodations.edit', ['selection', 'accommodation']],
    'criteria' => ['userzone.selections.criteria.index', ['selection']],
    'edit criterion' => ['userzone.selections.criteria.edit', ['selection', 'criterion']],
    'sharing' => ['userzone.selections.users.index', ['selection']],
]);

test('owners can open userzone pages', function (string $name, array $bindings) {
    $selection = Selection::factory()->create();
    $parameters = [];
    foreach ($bindings as $binding) {
        $parameters[$binding] = match ($binding) {
            'selection' => $selection,
            'accommodation' => Accommodation::factory()->for($selection)->create(),
            'criterion' => Criterion::factory()->for($selection)->create(),
        };
    }
    actingAs($selection->owner);

    get(route($name, $parameters))->assertOk();
})->with('userzone pages');

test('guests are redirected from userzone pages', function (string $name, array $bindings) {
    $parameters = array_fill_keys($bindings, 1);

    get(route($name, $parameters))->assertRedirect(route('login'));
})->with('userzone pages');
