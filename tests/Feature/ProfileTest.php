<?php

declare(strict_types=1);

use Tests\Factories\InstitutionFactory;
use Tests\Factories\UserFactory;

use function Pest\Laravel\actingAs;

test('profile page is displayed', function (): void {
    $user = UserFactory::new()->createOne();

    $response = actingAs($user)
        ->get(route('profile.edit'));

    $response->assertOk();
});

test('profile information cannot be updated', function (): void {
    InstitutionFactory::new(['domain' => 'example.com'])->createOne();
    $user = UserFactory::new()->createOne(['name' => 'John Doe']);

    $response = actingAs($user)
        ->patch(route('profile.update'), [
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit'));

    $user->refresh();

    expect($user->name)->toBe('JOHN DOE')
        ->and($user->email)->toBe('test@example.com')
        ->and($user->email_verified_at)->toBeNull();
});

test('email verification status is unchanged when the email address is unchanged', function (): void {
    InstitutionFactory::new(['domain' => 'example.com'])->createOne();
    $user = UserFactory::new()->createOne(['email' => 'test@example.com']);

    $response = actingAs($user)
        ->patch(route('profile.update'), [
            'email' => $user->email,
            'name' => 'Test User',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit'));

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

test('user can delete their account', function (): void {
    $user = UserFactory::new()->createOne();

    $response = actingAs($user)
        ->delete(route('profile.destroy'), [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard'));
});
