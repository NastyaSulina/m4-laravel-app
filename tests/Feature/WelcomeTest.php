<?php

use App\Models\Selection;
use App\Models\User;
use Illuminate\Support\Facades\File;

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

it('does not expose selections to guests', function () {
    Selection::factory()->create(['title' => 'Private travel plans']);

    get(route('welcome'))->assertOk()
        ->assertSee(route('login'))
        ->assertSee(route('register'))
        ->assertDontSeeText('Private travel plans');
});

it('offers a first selection to a new user', function () {
    $user = User::factory()->create();
    actingAs($user);

    get(route('welcome'))->assertOk()
        ->assertSeeText('Start a new selection')
        ->assertSee(route('userzone.selections.create'));
});

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

it('renders the current session csrf token when welcome data is cached', function () {
    $selection = Selection::factory()->create(['status' => 'active']);
    actingAs($selection->owner);
    $firstToken = str_repeat('a', 40);
    $secondToken = str_repeat('b', 40);
    $this->withSession(['_token' => $firstToken]);
    get(route('welcome'))->assertOk()
        ->assertSee('name="_token" value="'.$firstToken.'"', false);
    session()->invalidate();
    $this->withSession(['_token' => $secondToken]);

    get(route('welcome'))->assertOk()
        ->assertSee('name="_token" value="'.$secondToken.'"', false)
        ->assertDontSee($firstToken);
});

it('renders the latest selection on repeated requests using the file cache', function () {
    $cachePath = sys_get_temp_dir().'/staylist-welcome-cache-'.uniqid();
    config([
        'cache.default' => 'welcome-test',
        'cache.stores.welcome-test' => [
            'driver' => 'file',
            'path' => $cachePath,
            'serializable_classes' => false,
        ],
    ]);
    $selection = Selection::factory()->create(['title' => 'Madrid shortlist', 'status' => 'active']);
    actingAs($selection->owner);

    try {
        get(route('welcome'))->assertOk()->assertSeeText('Madrid shortlist');

        get(route('welcome'))->assertOk()
            ->assertSeeText('Madrid shortlist')
            ->assertSee(route('userzone.selections.show', $selection));
    } finally {
        File::deleteDirectory($cachePath);
    }
});
