<?php

use App\Models\Selection;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

it('lists only users sharing this selection and provides an email form', function () {
    $selection = Selection::factory()->create();
    $member = User::factory()->create();
    $other = User::factory()->create();
    $selection->users()->attach($member);
    actingAs($selection->owner);

    get(route('userzone.selections.users.index', $selection))->assertOk()
        ->assertSeeText($member->email)->assertDontSeeText($other->email)
        ->assertSee('name="email"', false);
});

it('adds an existing user once and preserves other members', function () {
    $selection = Selection::factory()->create();
    $member = User::factory()->create(['email' => 'member@example.com']);
    $existing = User::factory()->create();
    $selection->users()->attach($existing);
    actingAs($selection->owner);

    foreach (['member@example.com', 'member@example.com'] as $email) {
        post(route('userzone.selections.users.store', $selection), ['email' => $email])
            ->assertSessionHasNoErrors()->assertRedirect(route('userzone.selections.users.index', $selection));
    }
    expect($selection->users()->pluck('users.id')->sort()->values()->all())
        ->toBe(collect([$member->id, $existing->id])->sort()->values()->all());

    actingAs($member);
    get(route('userzone.selections.show', $selection))->assertOk();
    get(route('userzone.selections.index', ['filter' => 'shared']))->assertOk()->assertSeeText($selection->title);
});

it('rejects invalid emails without adding users', function (mixed $email) {
    $selection = Selection::factory()->create();
    actingAs($selection->owner);

    post(route('userzone.selections.users.store', $selection), ['email' => $email])->assertSessionHasErrors('email');
    expect($selection->users()->count())->toBe(0);
})->with([null, 'invalid', 'missing@example.com', [['bad@example.com']]]);

it('does not add the owner to the pivot', function () {
    $selection = Selection::factory()->create();
    actingAs($selection->owner);
    post(route('userzone.selections.users.store', $selection), ['email' => $selection->owner->email])->assertSessionHasErrors('email');
    expect($selection->users()->count())->toBe(0);
});

it('only allows the owner to list and add users', function (bool $isMember) {
    $selection = Selection::factory()->create();
    $user = User::factory()->create();
    $recipient = User::factory()->create();
    if ($isMember) {
        $selection->users()->attach($user);
    }
    actingAs($user);

    get(route('userzone.selections.users.index', $selection))->assertForbidden();
    post(route('userzone.selections.users.store', $selection), ['email' => $recipient->email])->assertForbidden();
    expect($selection->users()->whereKey($recipient->id)->exists())->toBeFalse();
})->with([true, false]);

it('redirects guests to login', function () {
    $selection = Selection::factory()->create();
    get(route('userzone.selections.users.index', $selection))->assertRedirect(route('login'));
    post(route('userzone.selections.users.store', $selection), ['email' => 'member@example.com'])->assertRedirect(route('login'));
});

it('lets the owner revoke only the selected membership without deleting the user', function () {
    $selection = Selection::factory()->create();
    $otherSelection = Selection::factory()->create();
    $member = User::factory()->create();
    $remaining = User::factory()->create();
    $selection->users()->attach([$member->id, $remaining->id]);
    $otherSelection->users()->attach($member);
    actingAs($selection->owner);

    get(route('userzone.selections.users.index', $selection))->assertOk()
        ->assertSee(route('userzone.selections.users.destroy', [$selection, $member]));
    delete(route('userzone.selections.users.destroy', [$selection, $member]))
        ->assertRedirect(route('userzone.selections.users.index', $selection));

    expect($selection->users()->pluck('users.id')->all())->toBe([$remaining->id]);
    expect($otherSelection->users()->whereKey($member->id)->exists())->toBeTrue();
    assertModelExists($member);
    actingAs($member);
    get(route('userzone.selections.show', $selection))->assertForbidden();
    get(route('userzone.selections.index', ['filter' => 'shared']))->assertOk()->assertDontSeeText($selection->title);
});

it('forbids revoking access by a member or outsider', function (bool $isMember) {
    $selection = Selection::factory()->create();
    $member = User::factory()->create();
    $actor = User::factory()->create();
    $selection->users()->attach($member);
    if ($isMember) {
        $selection->users()->attach($actor);
    }
    actingAs($actor);

    delete(route('userzone.selections.users.destroy', [$selection, $member]))->assertForbidden();
    expect($selection->users()->whereKey($member->id)->exists())->toBeTrue();
})->with([true, false]);

it('does not revoke a membership belonging to another selection', function () {
    $selection = Selection::factory()->create();
    $otherSelection = Selection::factory()->create();
    $member = User::factory()->create();
    $otherSelection->users()->attach($member);
    actingAs($selection->owner);

    delete(route('userzone.selections.users.destroy', [$selection, $member]))->assertNotFound();
    expect($otherSelection->users()->whereKey($member->id)->exists())->toBeTrue();
});

it('requires login to revoke access', function () {
    $selection = Selection::factory()->create();
    $member = User::factory()->create();
    $selection->users()->attach($member);

    delete(route('userzone.selections.users.destroy', [$selection, $member]))->assertRedirect(route('login'));
    expect($selection->users()->whereKey($member->id)->exists())->toBeTrue();
});
