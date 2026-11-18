<?php

use App\Models\Accommodation;
use App\Models\Criterion;
use App\Models\Selection;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\seed;

test('guests are redirected to login from userzone content', function (string $path) {
    get($path)->assertRedirect(route('login'));
})->with(['/user/selections', '/user/selections/1', '/user/selections/1/accommodations/1']);

test('authenticated users can open a selection by its URL', function () {
    $user = User::factory()->create();
    $selection = Selection::factory()->create();
    actingAs($user);

    get(route('userzone.selections.show', $selection))->assertOk();
});

test('authenticated users can open an accommodation by its URL', function () {
    $user = User::factory()->create();
    $accommodation = Accommodation::factory()->for($user)->create();
    actingAs($user);

    get(route('userzone.selections.accommodations.show', [$accommodation->selection_id, $accommodation]))->assertOk();
});

test('seeded test user can log in and browse their selection and accommodation', function () {
    seed(DatabaseSeeder::class);
    $user = User::where('email', 'test@example.com')->sole();
    $selection = $user->selections()->firstOrFail();
    $accommodation = $selection->accommodations()->firstOrFail();

    post(route('login'), ['email' => 'test@example.com', 'password' => 'password'])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    get(route('userzone.selections.index'))->assertOk()->assertSeeText($selection->title);
    get(route('userzone.selections.show', $selection))->assertOk()->assertSeeText($accommodation->title);
    get(route('userzone.selections.accommodations.show', [$accommodation->selection_id, $accommodation]))->assertOk();
});

test('old public content routes are unavailable', function (string $path) {
    get($path)->assertNotFound();
})->with(['/selections', '/selections/1', '/accommodations/1']);

test('nested routes reject records from another selection', function () {
    $user = User::factory()->create();
    $selection = Selection::factory()->for($user, 'owner')->create();
    $otherSelection = Selection::factory()->for($user, 'owner')->create();
    $criterion = Criterion::factory()->for($otherSelection)->create(['title' => 'Original']);
    $accommodation = Accommodation::factory()->for($otherSelection)->create();
    actingAs($user);

    get(route('userzone.selections.accommodations.show', [$selection, $accommodation]))->assertNotFound();
    \Pest\Laravel\delete(route('userzone.selections.criteria.destroy', [$selection, $criterion]))->assertNotFound();

    expect($criterion->fresh()->title)->toBe('Original');
});
