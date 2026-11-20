<?php

use App\Models\Accommodation;
use App\Models\Selection;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('accommodation page displays its details', function (string $propertyType, ?string $note) {
    $user = User::factory()->create();
    actingAs($user);

    $selection = Selection::factory()->for($user, 'owner')->create();
    $accommodation = Accommodation::factory()->for($selection)->for($user)->create([
        'title' => 'Madrid apartment',
        'address' => 'Calle Mayor 1',
        'status' => 'shortlisted',
        'source_url' => 'https://example.com/listing/1',
        'property_type' => $propertyType,
        'note' => $note,
    ]);

    $response = get(route('userzone.selections.accommodations.show', [$accommodation->selection_id, $accommodation]))
        ->assertOk()
        ->assertSee('<title>Madrid apartment | Staylist</title>', false)
        ->assertSeeText('Address: Calle Mayor 1')
        ->assertSeeText('Status: Shortlisted')
        ->assertSeeText('Source: https://example.com/listing/1')
        ->assertSee(route('userzone.selections.show', $accommodation->selection_id));

    $response->assertSeeText('Property type: '.ucfirst($propertyType));

    if ($note !== null) {
        $response->assertSeeText('Note: '.$note)
            ->assertDontSee('<script>alert(1)</script>', false);
    } else {
        $response->assertDontSeeText('Note:');
    }
})->with([
    'all fields' => ['apartment', '<script>alert(1)</script>'],
    'house without note' => ['house', null],
    'type only' => ['room', null],
    'hotel with note' => ['hotel', 'Near the station'],
]);

test('accommodation page returns 404 for a missing accommodation', function () {
    $user = User::factory()->create();
    actingAs($user);

    get(route('userzone.selections.accommodations.show', [Selection::factory()->create(), 999]))->assertNotFound();
});
