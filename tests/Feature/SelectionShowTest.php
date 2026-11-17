<?php

use App\Models\Criterion;
use App\Models\Accommodation;
use App\Models\Selection;

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('selection page displays the selected selection', function (?string $startDate, ?string $endDate) {
    $user = User::factory()->create();
    actingAs($user);

    $selection = Selection::factory()->for($user, 'owner')->create([
        'title' => '<script>alert(1)</script>',
        'search_type' => 'trip',
        'status' => 'active',
        'start_date' => $startDate,
        'end_date' => $endDate,
    ]);
    Selection::factory()->for($user, 'owner')->create(['title' => 'Another selection']);

    $response = get(route('userzone.selections.show', $selection))
        ->assertOk()
        ->assertSee('<title>&lt;script&gt;alert(1)&lt;/script&gt;</title>', false)
        ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
        ->assertDontSee('<script>alert(1)</script>', false)
        ->assertSeeText('Search type: trip')
        ->assertSeeText('Status: active')
        ->assertDontSeeText('Another selection')
        ->assertSee(route('userzone.selections.index'));
    $response->assertSeeText('No accommodations yet.');

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

    get(route('userzone.selections.show', $selection))
        ->assertOk()
        ->assertSeeText('Wi-Fi — Required')
        ->assertSeeText('Balcony — Optional')
        ->assertSee('action="'.route('userzone.criteria.destroy', $criterion).'"', false)
        ->assertSee('name="_method" value="delete"', false)
        ->assertSee('name="_token"', false)
        ->assertSee('aria-label="Delete criterion: Wi-Fi"', false)
        ->assertDontSeeText('Unrelated criterion');
});

test('selection page hides the criteria section when it has no criteria', function () {
    $user = User::factory()->create();
    actingAs($user);

    $selection = Selection::factory()->for($user, 'owner')->create();

    get(route('userzone.selections.show', $selection))
        ->assertOk()
        ->assertDontSeeText('Criteria')
        ->assertDontSeeText('No criteria yet.');
});

test('selection page lists only its own accommodations', function () {
    $user = User::factory()->create();
    actingAs($user);

    $selection = Selection::factory()->for($user, 'owner')->create();
    $accommodation = Accommodation::factory()->for($selection)->create([
        'title' => 'Madrid apartment',
        'address' => 'Calle Mayor 1',
        'status' => 'shortlisted',
        'property_type' => 'apartment',
    ]);
    Accommodation::factory()->create(['title' => 'Unrelated accommodation']);

    get(route('userzone.selections.show', $selection))
        ->assertOk()
        ->assertSeeText('Madrid apartment')
        ->assertSeeText('Address: Calle Mayor 1')
        ->assertSeeText('Status: shortlisted')
        ->assertSeeText('Property type: apartment')
        ->assertSee(route('userzone.accommodations.show', $accommodation))
        ->assertDontSeeText('Unrelated accommodation');
});
