<?php

use function Pest\Laravel\get;

it('returns a successful response', function () {
    $response = get('/');

    $response->assertStatus(200);
    $response->assertSee('<title>Welcome</title>', false);
});
