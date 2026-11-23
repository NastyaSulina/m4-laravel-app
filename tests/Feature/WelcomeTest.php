<?php

use App\Models\Selection;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('shows the latest created active owned selection without exposing other selections', function () {
    $user = User::factory()->create();
    $latest = Selection::factory()->for($user, 'owner')->create([
        'title' => '<script>Latest trip</script>',
        'status' => 'active',
        'created_at' => '2026-02-01',
        'updated_at' => '2026-02-01',
    ]);
    Selection::factory()->for($user, 'owner')->create([
        'title' => 'Older trip',
        'status' => 'active',
        'created_at' => '2026-01-01',
        'updated_at' => '2026-03-01',
    ]);
    Selection::factory()->for($user, 'owner')->create(['title' => 'Archived trip', 'status' => 'archived', 'created_at' => '2026-05-01']);
    $shared = Selection::factory()->create(['title' => 'Shared trip', 'status' => 'active', 'created_at' => '2026-04-01']);
    $shared->users()->attach($user);
    Selection::factory()->create(['title' => 'Private trip']);
    actingAs($user);

    get(route('welcome'))->assertOk()
        ->assertSee('&lt;script&gt;Latest trip&lt;/script&gt;', false)
        ->assertDontSee('<script>Latest trip</script>', false)
        ->assertSee(route('userzone.selections.show', $latest))
        ->assertDontSeeText('Older trip')
        ->assertDontSeeText('Archived trip')
        ->assertDontSeeText('Shared trip')
        ->assertDontSeeText('Private trip');
});

it('offers a first selection when the user only has shared selections', function () {
    $user = User::factory()->create();
    $selection = Selection::factory()->create(['title' => 'Someone elses trip']);
    $selection->users()->attach($user);
    actingAs($user);

    get(route('welcome'))->assertOk()
        ->assertSeeText('Start a new selection')
        ->assertSee(route('userzone.selections.create'))
        ->assertDontSeeText('Someone elses trip');
});

it('does not expose selections to guests', function () {
    Selection::factory()->create(['title' => 'Private travel plans']);

    get(route('welcome'))->assertOk()
        ->assertSee(route('login'))
        ->assertSee(route('register'))
        ->assertDontSeeText('Private travel plans');
});

it('offers a new selection when there are no active owned selections', function (bool $hasArchivedSelection) {
    $user = User::factory()->create();
    if ($hasArchivedSelection) {
        Selection::factory()->for($user, 'owner')->create(['title' => 'Archived trip', 'status' => 'archived']);
    }
    actingAs($user);

    get(route('welcome'))->assertOk()
        ->assertSeeText('Start a new selection')
        ->assertSee(route('userzone.selections.create'))
        ->assertDontSeeText('Archived trip');
})->with([false, true]);

it('keeps cached welcome selections separate for each user', function () {
    $first = Selection::factory()->create(['title' => 'First private trip', 'status' => 'active']);
    $second = Selection::factory()->create(['title' => 'Second private trip', 'status' => 'active']);

    actingAs($first->owner);
    get(route('welcome'))->assertOk()->assertSeeText('First private trip')->assertDontSeeText('Second private trip');

    actingAs($second->owner);
    get(route('welcome'))->assertOk()->assertSeeText('Second private trip')->assertDontSeeText('First private trip');

    actingAs($first->owner);
    get(route('welcome'))->assertOk()->assertSeeText('First private trip')->assertDontSeeText('Second private trip');
});

it('refreshes welcome after the fresh or stale period', function (int $seconds, bool $servesStale) {
    /** @var TestCase $this */
    $this->freezeTime();
    $selection = Selection::factory()->create(['title' => 'Original title', 'status' => 'active']);
    actingAs($selection->owner);
    get(route('welcome'))->assertOk()->assertSeeText('Original title');

    $selection->updateQuietly(['title' => 'Changed outside model events']);
    $this->travel(29)->seconds();
    get(route('welcome'))->assertOk()->assertSeeText('Original title')->assertDontSeeText('Changed outside model events');

    $this->travel($seconds)->seconds();
    $response = get(route('welcome'))->assertOk();
    if ($servesStale) {
        $response->assertSeeText('Original title')->assertDontSeeText('Changed outside model events');
    } else {
        $response->assertSeeText('Changed outside model events')->assertDontSeeText('Original title');
    }

    get(route('welcome'))->assertOk()->assertSeeText('Changed outside model events')->assertDontSeeText('Original title');
})->with([
    'stale at 31 seconds' => [2, true],
    'expired at 61 seconds' => [32, false],
]);

it('caches an empty result and invalidates it when a selection is created', function () {
    $user = User::factory()->create();
    actingAs($user);
    get(route('welcome'))->assertOk()->assertSeeText('Start a new selection');

    DB::enableQueryLog();
    get(route('welcome'))->assertOk()->assertSeeText('Start a new selection');
    $queries = DB::getQueryLog();
    DB::disableQueryLog();
    expect(collect($queries)->filter(fn (array $query) => str_contains($query['query'], '"selections"')))->toBeEmpty();

    Selection::factory()->for($user, 'owner')->create(['title' => 'New active trip', 'status' => 'active']);
    get(route('welcome'))->assertOk()->assertSeeText('New active trip');
});

it('refreshes the owners welcome when a shared member changes a selection', function (string $change) {
    $selection = Selection::factory()->create(['title' => 'Original trip', 'status' => 'active']);
    $owner = $selection->owner;
    $member = User::factory()->create();
    $selection->users()->attach($member);
    actingAs($owner);
    get(route('welcome'))->assertOk()->assertSeeText('Original trip');

    actingAs($member);
    if ($change === 'delete') {
        $selection->delete();
    } else {
        $selection->update($change === 'archive' ? ['status' => 'archived'] : ['title' => 'Updated trip']);
    }

    actingAs($owner);
    $response = get(route('welcome'))->assertOk()->assertDontSeeText('Original trip');
    if ($change === 'rename') {
        $response->assertSeeText('Updated trip');
    } else {
        $response->assertSeeText('Start a new selection');
    }
})->with(['rename', 'archive', 'delete']);

it('caches the rendered welcome page separately for guests and users', function () {
    $renders = 0;
    View::composer('welcome', function () use (&$renders): void {
        $renders++;
    });

    get(route('welcome'))->assertOk()->assertSee(route('login'));
    get(route('welcome'))->assertOk()->assertSee(route('login'));
    expect($renders)->toBe(1);

    $selection = Selection::factory()->create(['title' => 'My private trip', 'status' => 'active']);
    actingAs($selection->owner);
    get(route('welcome'))->assertOk()->assertSeeText('My private trip')->assertDontSee(route('login'));
    get(route('welcome'))->assertOk()->assertSeeText('My private trip');
    expect($renders)->toBe(2);

    Auth::logout();
    get(route('welcome'))->assertOk()->assertSee(route('login'))->assertDontSeeText('My private trip');
    expect($renders)->toBe(2);
});
