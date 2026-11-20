<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('returns a successful response', function () {
    $response = get('/');

    $response->assertStatus(200);
    $response->assertSee('<title>Staylist</title>', false);
});

it('shows the logo and sign in and sign up links to guests', function () {
    get(route('welcome'))->assertOk()
        ->assertSee('href="'.route('welcome').'"', false)
        ->assertSee('src="'.asset('img/staylist-logo.png').'"', false)
        ->assertSee('alt="Staylist"', false)
        ->assertSee('href="'.route('login').'"', false)
        ->assertSee('href="'.route('register').'"', false)
        ->assertSeeText('Sign in')
        ->assertSeeText('Sign up')
        ->assertDontSeeText('My selections')
        ->assertDontSee('href="'.route('userzone.selections.index').'"', false)
        ->assertDontSee('href="'.route('dashboard').'"', false)
        ->assertDontSeeText('Home');
});

it('shows personal navigation without the user name to signed in users', function () {
    $user = User::factory()->create(['name' => 'Navigation Test User']);
    actingAs($user);

    get(route('welcome'))->assertOk()
        ->assertSee('href="'.route('welcome').'"', false)
        ->assertSee('alt="Staylist"', false)
        ->assertSee('href="'.route('userzone.selections.index').'"', false)
        ->assertSeeText('My selections')
        ->assertDontSeeText($user->name)
        ->assertSee('href="'.route('dashboard').'"', false)
        ->assertSeeText('Dashboard')
        ->assertDontSee('href="'.route('login').'"', false)
        ->assertDontSee('href="'.route('register').'"', false)
        ->assertDontSeeText('Sign in')
        ->assertDontSeeText('Sign up');
});
