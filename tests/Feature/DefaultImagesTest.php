<?php

use App\Models\Accommodation;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('accommodation without an image displays the fallback', function () {
    $accommodation = Accommodation::factory()->create();
    $selection = $accommodation->selection;
    actingAs($selection->owner);

    get(route('userzone.selections.accommodations.show', [$selection, $accommodation]))
        ->assertOk()->assertSee('src="/img/default-accommodation.png"', false);
});
