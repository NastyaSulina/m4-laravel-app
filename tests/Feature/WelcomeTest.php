<?php

use App\Models\Selection;
use App\Models\User;

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
        ->assertViewHas('latestSelection', null)
        ->assertSee(route('userzone.selections.create'))
        ->assertDontSeeText('Archived trip');
})->with([false, true]);
