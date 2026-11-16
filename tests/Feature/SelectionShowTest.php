<?php

use App\Models\Selection;

use function Pest\Laravel\get;

test('selection page displays the selected selection', function (?string $startDate, ?string $endDate) {
    $selection = Selection::factory()->create([
        'title' => '<script>alert(1)</script>',
        'search_type' => 'trip',
        'status' => 'active',
        'start_date' => $startDate,
        'end_date' => $endDate,
    ]);
    Selection::factory()->create(['title' => 'Another selection']);

    $response = get(route('selections.show', $selection))
        ->assertOk()
        ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
        ->assertDontSee('<script>alert(1)</script>', false)
        ->assertSeeText('Search type: trip')
        ->assertSeeText('Status: active')
        ->assertDontSeeText('Another selection')
        ->assertSee(route('selections.index'));

    if ($startDate !== null) {
        $response->assertSeeText('Start date: '.$startDate);
    } else {
        $response->assertDontSeeText('Start date:');
    }

    if ($endDate !== null) {
        $response->assertSeeText('End date: '.$endDate);
    } else {
        $response->assertDontSeeText('End date:');
    }
})->with([
    'both dates' => ['2027-06-01', '2027-06-15'],
    'start only' => ['2027-06-01', null],
    'end only' => [null, '2027-06-15'],
    'neither date' => [null, null],
]);

test('selection page returns 404 for a missing selection', function () {
    get(route('selections.show', 999))->assertNotFound();
});
