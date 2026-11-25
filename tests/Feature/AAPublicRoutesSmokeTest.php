<?php

use function Pest\Laravel\get;

test('public pages are reachable', function (string $name, array $parameters) {
    get(route($name, $parameters))->assertOk();
})->with([
    'welcome' => ['welcome', []],
    'login' => ['login', []],
    'registration' => ['register', []],
    'forgot password' => ['password.request', []],
    'reset password' => ['password.reset', ['token' => 'smoke-test-token']],
]);
