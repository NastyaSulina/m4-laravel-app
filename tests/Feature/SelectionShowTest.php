<?php

use App\Models\Accommodation;
use App\Models\Criterion;
use App\Models\Selection;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('selection page displays the selected selection', function (?string $startDate, ?string $endDate) {
    $user = User::factory()->create();
    actingAs($user);

    $selection = Selection::factory()->for($user, 'owner')->create([
        'title' => '<script>alert(1)</script>',
        'search_type' => 'short_stay',
        'status' => 'active',
        'start_date' => $startDate,
        'end_date' => $endDate,
    ]);
    Selection::factory()->for($user, 'owner')->create(['title' => 'Another selection']);

    $response = get(route('userzone.selections.show', $selection))
        ->assertOk()
        ->assertSee('<title>&lt;script&gt;alert(1)&lt;/script&gt; | Staylist</title>', false)
        ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
        ->assertDontSee('<script>alert(1)</script>', false)
        ->assertSeeText('Short stay')
        ->assertSeeText('Status: Active')
        ->assertDontSeeText('Another selection')
        ->assertSee(route('userzone.selections.index'));
    $response->assertSeeText('No accommodations yet.');

    if ($startDate !== null) {
        $response->assertSeeText('From: '.$startDate);
    } else {
        $response->assertDontSeeText('From: ');
    }

    if ($endDate !== null) {
        $response->assertSeeText('To: '.$endDate);
    } else {
        $response->assertDontSeeText('To: ');
    }
})->with([
    'both dates' => ['2027-06-01', '2027-06-15'],
    'start only' => ['2027-06-01', null],
    'end only' => [null, '2027-06-15'],
    'neither date' => [null, null],
]);

test('selection page returns 404 for a missing selection', function () {
    $user = User::factory()->create();
    actingAs($user);

    get(route('userzone.selections.show', 999))->assertNotFound();
});

test('selection page lists only its own criteria with their requirement', function () {
    $user = User::factory()->create();
    actingAs($user);

    $selection = Selection::factory()->for($user, 'owner')->create();
    $criterion = Criterion::factory()->for($selection)->create(['title' => 'Wi-Fi', 'is_required' => true]);
    Criterion::factory()->for($selection)->create(['title' => 'Balcony', 'is_required' => false]);
    Criterion::factory()->create(['title' => 'Unrelated criterion']);

    $requiredMarker = '<span title="Required" aria-hidden="true">*</span>';

    $response = get(route('userzone.selections.show', $selection))
        ->assertOk()
        ->assertSeeText('Wi-Fi')
        ->assertSeeText('Balcony')
        ->assertSee('<span class="sr-only">Required</span>', false)
        ->assertSeeInOrder(['Wi-Fi', $requiredMarker, 'Balcony'], false)
        ->assertSee(route('userzone.selections.criteria.index', $selection))
        ->assertDontSee(route('userzone.selections.criteria.edit', [$selection, $criterion]))
        ->assertDontSeeText('Add criterion')
        ->assertDontSee('action="'.route('userzone.selections.criteria.destroy', [$selection, $criterion]).'"', false)
        ->assertDontSeeText('Unrelated criterion');

    expect(substr_count($response->getContent(), $requiredMarker))->toBe(1);
});

test('selection page offers criteria management when it has no criteria', function () {
    $user = User::factory()->create();
    actingAs($user);

    $selection = Selection::factory()->for($user, 'owner')->create();

    get(route('userzone.selections.show', $selection))
        ->assertOk()
        ->assertSeeText('Manage criteria')
        ->assertDontSeeText('No criteria yet.');
});

test('selection page lists only its own accommodations', function () {
    $user = User::factory()->create();
    actingAs($user);

    $selection = Selection::factory()->for($user, 'owner')->create();
    $accommodation = Accommodation::factory()->for($selection)->create([
        'title' => 'Madrid apartment',
        'note' => '<script>alert(2)</script>',
        'address' => 'Calle Mayor 1',
        'status' => 'shortlisted',
        'property_type' => 'apartment',
    ]);
    Accommodation::factory()->create(['title' => 'Unrelated accommodation']);

    get(route('userzone.selections.show', $selection))
        ->assertOk()
        ->assertSeeText('Madrid apartment')
        ->assertSeeText('Address: Calle Mayor 1')
        ->assertDontSee($accommodation->note)
        ->assertDontSee($accommodation->note, false)
        ->assertSeeText('Status: Shortlisted')
        ->assertSeeText('Property type: Apartment')
        ->assertSee(route('userzone.selections.accommodations.show', [$accommodation->selection_id, $accommodation]))
        ->assertDontSeeText('Unrelated accommodation');
});
