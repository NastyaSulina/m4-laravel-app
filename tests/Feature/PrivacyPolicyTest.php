<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('keeps navigation and csrf tokens outside the shared content cache', function () {
    actingAs(User::factory()->create());
    $signedInToken = str_repeat('a', 40);
    $guestToken = str_repeat('b', 40);
    $this->withSession(['_token' => $signedInToken]);
    get(route('privacy-policy'))->assertOk()
        ->assertSee('name="_token" value="'.$signedInToken.'"', false)
        ->assertSee('action="'.route('logout').'"', false);
    Auth::logout();
    session()->invalidate();
    $this->withSession(['_token' => $guestToken]);

    get(route('privacy-policy'))->assertOk()
        ->assertSee('href="'.route('login').'"', false)
        ->assertSee('name="csrf-token" content="'.$guestToken.'"', false)
        ->assertDontSee($signedInToken)
        ->assertDontSee('action="'.route('logout').'"', false);
});
