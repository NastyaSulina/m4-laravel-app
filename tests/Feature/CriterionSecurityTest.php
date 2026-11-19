<?php

use App\Models\Criterion;
use App\Models\Selection;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\postJson;
use function Pest\Laravel\put;
use function Pest\Laravel\putJson;

test('criteria endpoints reject unauthorized callers without changing data', function (string $method, string $action, bool $guest) {
    $criterion = Criterion::factory()->create(['title' => 'Private criterion', 'is_required' => true])->refresh();
    $selection = $criterion->selection;
    if (! $guest) {
        actingAs(User::factory()->create());
    }
    $response = test()->call($method, route('userzone.selections.criteria.'.$action, [$selection, $criterion]), [
        'title' => 'Tampered criterion', 'is_required' => 0,
    ]);

    if ($guest) {
        $response->assertRedirect(route('login'));
    } else {
        $response->assertForbidden()->assertDontSeeText('Private criterion');
    }
    assertDatabaseCount('criteria', 1);
    expect($criterion->fresh()->getAttributes())->toBe($criterion->getAttributes());
})->with([
    ['GET', 'index'], ['GET', 'edit'], ['POST', 'store'],
    ['PUT', 'update'], ['PATCH', 'update'], ['DELETE', 'destroy'],
])->with([true, false]);

test('a foreign criterion cannot be accessed through an owned selection', function (string $method, string $action) {
    $selection = Selection::factory()->create();
    $foreign = Criterion::factory()->create()->refresh();
    actingAs($selection->owner);

    test()->call($method, route('userzone.selections.criteria.'.$action, [$selection, $foreign]), [
        'title' => 'Tampered', 'is_required' => 0,
    ])->assertNotFound();

    expect($foreign->fresh()->getAttributes())->toBe($foreign->getAttributes());
})->with([['GET', 'edit'], ['PUT', 'update'], ['PATCH', 'update'], ['DELETE', 'destroy']]);

test('criterion writes ignore injected identity and ownership fields', function (string $method, string $action) {
    $criterion = Criterion::factory()->create()->refresh();
    $selection = $criterion->selection;
    $foreign = Criterion::factory()->create()->refresh();
    actingAs($selection->owner);

    test()->call($method, route('userzone.selections.criteria.'.$action, [$selection, $criterion]), [
        'title' => 'Valid title', 'is_required' => 1,
        'id' => $foreign->id, 'selection_id' => $foreign->selection_id,
        'owner_id' => $foreign->selection->owner_id, 'user_id' => $foreign->selection->owner_id,
        'is_admin' => true, 'created_at' => '2000-01-01 00:00:00', 'updated_at' => '2000-01-01 00:00:00',
    ])->assertSessionHasNoErrors()->assertRedirect(route('userzone.selections.criteria.index', $selection));

    $saved = $selection->criteria()->where('title', 'Valid title')->sole();
    expect($saved->selection_id)->toBe($selection->id);
    expect($saved->id)->not->toBe($foreign->id);
    expect($saved->created_at->year)->not->toBe(2000);
    expect($saved->updated_at->year)->not->toBe(2000);
    expect($foreign->fresh()->getAttributes())->toBe($foreign->getAttributes());
    expect((bool) $saved->is_required)->toBeTrue();
    assertDatabaseCount('criteria', $action === 'store' ? 3 : 2);
})->with([['POST', 'store'], ['PUT', 'update']]);

test('invalid criterion payloads are rejected before database changes', function (array $invalid, string $field, string $method, string $action) {
    $criterion = Criterion::factory()->create()->refresh();
    actingAs($criterion->selection->owner);

    test()->call($method, route('userzone.selections.criteria.'.$action, [$criterion->selection_id, $criterion]), array_replace([
        'title' => 'Valid title', 'is_required' => true,
    ], $invalid))->assertSessionHasErrors($field);

    assertDatabaseCount('criteria', 1);
    expect($criterion->fresh()->getAttributes())->toBe($criterion->getAttributes());
})->with([
    'null title' => [['title' => null], 'title'],
    'empty title' => [['title' => ''], 'title'],
    'whitespace title' => [['title' => " \t\n "], 'title'],
    'zero width spaces' => [['title' => "\u{200B}\u{200B}\u{200B}"], 'title'],
    'short after trimming' => [['title' => ' a '], 'title'],
    'numeric title' => [['title' => 123], 'title'],
    'array title' => [['title' => ['nested']], 'title'],
    'boolean title' => [['title' => true], 'title'],
    '256 characters' => [['title' => str_repeat('a', 256)], 'title'],
    '256 unicode characters' => [['title' => str_repeat('я', 256)], 'title'],
    'very large title' => [['title' => str_repeat('a', 100000)], 'title'],
    'null checkbox' => [['is_required' => null], 'is_required'],
    'array checkbox' => [['is_required' => [1]], 'is_required'],
    'checkbox two' => [['is_required' => 2], 'is_required'],
    'checkbox negative' => [['is_required' => -1], 'is_required'],
    'checkbox true string' => [['is_required' => 'true'], 'is_required'],
    'checkbox on' => [['is_required' => 'on'], 'is_required'],
])->with([['POST', 'store'], ['PUT', 'update']]);

test('checkbox accepted representations persist consistently', function (mixed $value, bool $expected) {
    $criterion = Criterion::factory()->create(['is_required' => ! $expected]);
    actingAs($criterion->selection->owner);

    put(route('userzone.selections.criteria.update', [$criterion->selection_id, $criterion]), [
        'title' => 'Valid title', 'is_required' => $value,
    ])->assertSessionHasNoErrors()->assertRedirect();

    expect((bool) $criterion->fresh()->is_required)->toBe($expected);
})->with([[true, true], [false, false], [1, true], [0, false], ['1', true], ['0', false]]);

test('omitting checkbox resets an existing required criterion to false', function () {
    $criterion = Criterion::factory()->create(['is_required' => true]);
    actingAs($criterion->selection->owner);

    put(route('userzone.selections.criteria.update', [$criterion->selection_id, $criterion]), ['title' => 'Valid title'])
        ->assertSessionHasNoErrors()->assertRedirect();

    expect((bool) $criterion->fresh()->is_required)->toBeFalse();
});

test('SQL shaped titles remain literal text and do not affect other records', function () {
    $selection = Selection::factory()->create();
    $untouched = Criterion::factory()->create()->refresh();
    actingAs($selection->owner);
    $payload = "Parking'); DROP TABLE criteria; --";

    post(route('userzone.selections.criteria.store', $selection), ['title' => $payload])
        ->assertSessionHasNoErrors()->assertRedirect();

    expect($selection->criteria()->sole()->title)->toBe($payload);
    expect($untouched->fresh()->getAttributes())->toBe($untouched->getAttributes());
    assertDatabaseCount('criteria', 2);
});

test('stored HTML titles are escaped on all criterion surfaces', function (string $payload) {
    $selection = Selection::factory()->create();
    actingAs($selection->owner);
    post(route('userzone.selections.criteria.store', $selection), ['title' => $payload])
        ->assertSessionHasNoErrors()->assertRedirect();
    $criterion = $selection->criteria()->sole();
    expect($criterion->title)->toBe($payload);

    foreach ([
        route('userzone.selections.show', $selection),
        route('userzone.selections.criteria.index', $selection),
        route('userzone.selections.criteria.edit', [$selection, $criterion]),
    ] as $url) {
        get($url)->assertOk()->assertSee($payload)->assertDontSee($payload, false);
    }
})->with(['<script>alert(1)</script>', '<img src=x onerror=alert(1)>', '"><svg onload=alert(1)>']);

test('valid title length boundaries and unicode are accepted', function (string $title) {
    $selection = Selection::factory()->create();
    actingAs($selection->owner);

    post(route('userzone.selections.criteria.store', $selection), ['title' => $title])
        ->assertSessionHasNoErrors()->assertRedirect();

    expect($selection->criteria()->sole()->title)->toBe($title);
})->with(['abc', str_repeat('a', 255), 'Балкон', str_repeat('я', 255)]);

test('current rules permit punctuation numeric strings and duplicate titles', function (string $title) {
    $selection = Selection::factory()->create();
    actingAs($selection->owner);

    post(route('userzone.selections.criteria.store', $selection), ['title' => $title])->assertSessionHasNoErrors()->assertRedirect();
    post(route('userzone.selections.criteria.store', $selection), ['title' => $title])->assertSessionHasNoErrors()->assertRedirect();

    expect($selection->criteria()->where('title', $title)->count())->toBe(2);
})->with(['123', '---']);

test('criteria writes reject requests without a CSRF token when testing bypass is disabled', function (string $method, string $action) {
    app()->bind(PreventRequestForgery::class, fn ($app) => new class($app, $app['encrypter']) extends PreventRequestForgery
    {
        protected function runningUnitTests(): bool
        {
            return false;
        }
    });
    $criterion = Criterion::factory()->create()->refresh();
    actingAs($criterion->selection->owner);

    test()->call($method, route('userzone.selections.criteria.'.$action, [$criterion->selection_id, $criterion]), [
        'title' => 'Tampered', 'is_required' => 0,
    ])->assertStatus(419);

    assertDatabaseCount('criteria', 1);
    expect($criterion->fresh()->getAttributes())->toBe($criterion->getAttributes());
})->with([['POST', 'store'], ['PUT', 'update'], ['PATCH', 'update'], ['DELETE', 'destroy']]);

test('JSON does not bypass criterion validation', function () {
    $selection = Selection::factory()->create();
    actingAs($selection->owner);

    postJson(route('userzone.selections.criteria.store', $selection), [
        'title' => ['unexpected' => 'object'], 'is_required' => 'false',
    ])->assertUnprocessable()->assertJsonValidationErrors(['title', 'is_required']);

    assertDatabaseCount('criteria', 0);
});

test('JSON cannot update a foreign criterion by spoofing ownership in the body', function () {
    $criterion = Criterion::factory()->create()->refresh();
    $attacker = User::factory()->create();
    $owned = Selection::factory()->for($attacker, 'owner')->create();
    actingAs($attacker);

    putJson(route('userzone.selections.criteria.update', [$criterion->selection_id, $criterion]), [
        'title' => 'Tampered', 'selection_id' => $owned->id, 'owner_id' => $attacker->id,
    ])->assertForbidden();

    expect($criterion->fresh()->getAttributes())->toBe($criterion->getAttributes());
});

test('hidden method override does not bypass criterion ownership checks', function () {
    $criterion = Criterion::factory()->create()->refresh();
    actingAs(User::factory()->create());

    post(route('userzone.selections.criteria.destroy', [$criterion->selection_id, $criterion]), [
        '_method' => 'DELETE',
    ])->assertForbidden();

    expect($criterion->fresh()->getAttributes())->toBe($criterion->getAttributes());
});
