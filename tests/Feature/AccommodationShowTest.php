<?php

use App\Models\Accommodation;

use function Pest\Laravel\get;

test('accommodation page displays its details', function (?string $propertyType, ?string $note) {
    $accommodation = Accommodation::factory()->create([
        'title' => 'Madrid apartment',
        'address' => 'Calle Mayor 1',
        'status' => 'shortlisted',
        'source_url' => 'https://example.com/listing/1',
        'property_type' => $propertyType,
        'note' => $note,
    ]);

    $response = get(route('accommodations.show', $accommodation))
        ->assertOk()
        ->assertSee('<title>Madrid apartment</title>', false)
        ->assertSeeText('Address: Calle Mayor 1')
        ->assertSeeText('Status: shortlisted')
        ->assertSeeText('Source: https://example.com/listing/1')
        ->assertSee(route('selections.show', $accommodation->selection_id));

    if ($propertyType !== null) {
        $response->assertSeeText('Property type: '.$propertyType);
    } else {
        $response->assertDontSeeText('Property type:');
    }

    if ($note !== null) {
        $response->assertSeeText('Note: '.$note)
            ->assertDontSee('<script>alert(1)</script>', false);
    } else {
        $response->assertDontSeeText('Note:');
    }
})->with([
    'all fields' => ['apartment', '<script>alert(1)</script>'],
    'no optional fields' => [null, null],
    'type only' => ['room', null],
    'note only' => [null, 'Near the station'],
]);

test('accommodation page returns 404 for a missing accommodation', function () {
    get(route('accommodations.show', 999))->assertNotFound();
});
