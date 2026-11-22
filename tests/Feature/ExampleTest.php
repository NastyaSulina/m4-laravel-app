<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('returns a successful response', function () {
    $response = get('/');

    $response->assertStatus(200);
});

it('shows authentication links to guests', function () {
    get(route('welcome'))->assertOk()
        ->assertSee('href="'.route('welcome').'"', false)
        ->assertSee('href="'.route('login').'"', false)
        ->assertSee('href="'.route('register').'"', false)
        ->assertDontSee('href="'.route('userzone.selections.index').'"', false)
        ->assertDontSee('href="'.route('dashboard').'"', false);
});

it('shows personal navigation to signed in users', function () {
    $user = User::factory()->create();
    actingAs($user);

    get(route('welcome'))->assertOk()
        ->assertSee('href="'.route('welcome').'"', false)
        ->assertSee('href="'.route('userzone.selections.index').'"', false)
        ->assertSee('href="'.route('dashboard').'"', false)
        ->assertDontSee('href="'.route('login').'"', false)
        ->assertDontSee('href="'.route('register').'"', false);
});
