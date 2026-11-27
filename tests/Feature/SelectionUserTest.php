<?php

use App\Models\Selection;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

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

it('lets the owner revoke only the selected membership without deleting the user', function () {
    $selection = Selection::factory()->create();
    $otherSelection = Selection::factory()->create();
    $member = User::factory()->create();
    $remaining = User::factory()->create();
    $selection->users()->attach([$member->id, $remaining->id]);
    $otherSelection->users()->attach($member);
    actingAs($selection->owner);

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

it('keeps shared selections accessible after one owner deletes their account', function () {
    $member = User::factory()->create();
    $selection = Selection::factory()->create(['title' => 'Former owner selection']);
    $otherSelection = Selection::factory()->create(['title' => 'Remaining shared selection']);
    $selection->users()->attach($member);
    $otherSelection->users()->attach($member);

    actingAs($selection->owner);
    delete(route('profile.destroy'), ['password' => 'password'])->assertSessionHasNoErrors()->assertRedirect('/');

    actingAs($member);
    get(route('userzone.selections.index', ['filter' => 'shared']))
        ->assertOk()->assertDontSeeText($selection->title)->assertSeeText($otherSelection->title);
    get(route('userzone.selections.show', $selection))->assertNotFound();
});
