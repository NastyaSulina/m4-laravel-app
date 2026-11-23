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
        ->assertSeeText($typeLabel)
        ->assertSeeText($statusLabel);
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
        ->assertSee(route('userzone.selections.show', $selection))
        ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
        ->assertDontSee('<script>alert(1)</script>', false);
});

test('selections page displays an empty message for the selected filter', function (string $filter) {
    $user = User::factory()->create();
    actingAs($user);

    get(route('userzone.selections.index', ['filter' => $filter]))
        ->assertOk()
        ->assertSeeText('No selections yet.')
        ->assertSeeText('My selections')
        ->assertSeeText('Shared with me');
})->with([
    'my',
    'shared',
]);

test('selections page only displays dates that are present', function (?string $startDate, ?string $endDate) {
    $user = User::factory()->create();
    actingAs($user);

    Selection::factory()->for($user, 'owner')->create([
        'start_date' => $startDate,
        'end_date' => $endDate,
    ]);

    $response = get(route('userzone.selections.index'))->assertOk();

    if ($startDate !== null) {
        $response->assertSeeText('From: '.$startDate);
    } else {
        $response->assertDontSeeText('From:');
    }

    if ($endDate !== null) {
        $response->assertSeeText('To: '.$endDate);
    } else {
        $response->assertDontSeeText('To:');
    }
})->with([
    'both dates' => ['2027-06-01', '2027-06-15'],
    'start only' => ['2027-06-01', null],
    'end only' => [null, '2027-06-15'],
    'neither date' => [null, null],
]);

test('selection filters show only the appropriate accessible selections without duplicates', function (?string $filter, array $expectedTitles) {
    $user = User::factory()->create();
    $owned = Selection::factory()->for($user, 'owner')->create(['title' => 'Owned selection', 'created_at' => '2026-01-01']);
    $shared = Selection::factory()->create(['title' => 'Shared selection', 'created_at' => '2026-01-02']);
    $owned->users()->attach($user);
    $shared->users()->attach($user);
    Selection::factory()->create(['title' => 'Unrelated selection']);
    actingAs($user);

    $response = get(route('userzone.selections.index', $filter === null ? [] : ['filter' => $filter]));

    $response->assertOk();
    expect($response['selections']->pluck('title')->all())->toBe($expectedTitles);
    $response->assertViewHas('filter', $filter ?? 'my');
})->with([
    'default' => [null, ['Owned selection']],
    'my' => ['my', ['Owned selection']],
    'shared' => ['shared', ['Shared selection']],
]);

test('invalid selection filters fall back to owned selections', function (mixed $filter) {
    $user = User::factory()->create();
    $owned = Selection::factory()->for($user, 'owner')->create();
    $shared = Selection::factory()->create();
    $shared->users()->attach($user);
    actingAs($user);

    $response = get(route('userzone.selections.index', ['filter' => $filter]));

    $response->assertOk()->assertViewHas('filter', 'my');
    expect($response['selections']->modelKeys())->toBe([$owned->id]);
})->with([
    'unknown' => ['invalid'],
    'removed all filter' => ['all'],
    'empty' => [''],
    'array' => [['all']],
]);

test('shared cards identify the owner safely', function () {
    $user = User::factory()->create();
    $owner = User::factory()->create(['name' => '<script>alert(1)</script>']);
    $shared = Selection::factory()->for($owner, 'owner')->create();
    $shared->users()->attach($user);
    actingAs($user);

    get(route('userzone.selections.index', ['filter' => 'shared']))
        ->assertOk()
        ->assertSee('Owner: &lt;script&gt;alert(1)&lt;/script&gt;', false)
        ->assertDontSee('<script>alert(1)</script>', false)
        ->assertSee(route('userzone.selections.show', $shared));
});

test('selection lists include owned selections regardless of creation date', function () {
    $user = User::factory()->create();
    $newest = Selection::factory()->for($user, 'owner')->create(['created_at' => '2026-02-01']);
    $older = Selection::factory()->for($user, 'owner')->create(['created_at' => '2026-01-01']);
    $tie = Selection::factory()->for($user, 'owner')->create(['created_at' => '2026-01-01']);
    actingAs($user);

    $response = get(route('userzone.selections.index'));

    $response->assertOk();
    expect($response['selections']->modelKeys())->toEqualCanonicalizing([$newest->id, $tie->id, $older->id]);
});

test('the old shared selections route is no longer available', function () {
    actingAs(User::factory()->create());

    get('/user/shared-selections')->assertNotFound();
});

test('guests cannot access selection filters', function () {
    get(route('userzone.selections.index', ['filter' => 'shared']))
        ->assertRedirectToRoute('login');
});

test('shared members can view selection details', function () {
    $user = User::factory()->create();
    $selection = Selection::factory()->create();
    $selection->users()->attach($user);
    actingAs($user);

    get(route('userzone.selections.show', $selection))
        ->assertOk()
        ->assertSeeText($selection->title)
        ->assertSeeText($selection->owner->name);
});
