<?php

use App\Models\Selection;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('selection pages display readable search type and status', function (string $page, string $type, string $typeLabel, string $status, string $statusLabel) {
    $selection = Selection::factory()->create(['search_type' => $type, 'status' => $status]);
    actingAs($selection->owner);

    get(route('userzone.selections.'.$page, $page === 'show' ? $selection : []))
        ->assertOk()
        ->assertSeeText('Search type: '.$typeLabel)
        ->assertSeeText('Status: '.$statusLabel);
})->with(['index', 'show'])->with([
    ['short_stay', 'Short stay', 'active', 'Active'],
    ['long_term', 'Long-term rental', 'archived', 'Archived'],
]);

test('selection edit form selects its current status', function (string $status, string $label) {
    $selection = Selection::factory()->create(['status' => $status]);
    actingAs($selection->owner);

    get(route('userzone.selections.edit', $selection))
        ->assertOk()
        ->assertSee('name="status"', false)
        ->assertSee('<option value="'.$status.'" selected>'.$label.'</option>', false);
})->with([
    ['active', 'Active'],
    ['archived', 'Archived'],
]);

test('selections page displays only the current users selections', function () {
    $user = User::factory()->create();
    actingAs($user);

    Selection::factory()->create(['title' => 'Someone elses selection']);
    $selection = Selection::factory()->for($user, 'owner')->create(['title' => 'Summer in Madrid']);
    Selection::factory()->for($user, 'owner')->create(['title' => '<script>alert(1)</script>']);

    get(route('userzone.selections.index'))
        ->assertOk()
        ->assertSeeText('Summer in Madrid')
        ->assertDontSeeText('Someone elses selection')
        ->assertSee('<title>Selections</title>', false)
        ->assertSee(route('userzone.selections.show', $selection))
        ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
        ->assertDontSee('<script>alert(1)</script>', false);
});

test('selections page displays an empty message when there are no selections', function () {
    $user = User::factory()->create();
    actingAs($user);

    get(route('userzone.selections.index'))
        ->assertOk()
        ->assertSeeText('No selections yet.');
});

test('selections page only displays dates that are present', function (?string $startDate, ?string $endDate) {
    $user = User::factory()->create();
    actingAs($user);

    Selection::factory()->for($user, 'owner')->create([
        'start_date' => $startDate,
        'end_date' => $endDate,
    ]);

    $response = get(route('userzone.selections.index'))->assertOk();

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
