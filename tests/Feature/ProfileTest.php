<?php

declare(strict_types=1);

use Tests\Factories\UserFactory;

test('profile page is displayed', function (): void {
    $user = UserFactory::new()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk();
});

test('profile information can be updated', function (): void {
    $user = UserFactory::new()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('TEST USER', $user->name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertNull($user->email_verified_at);
});

test('email verification status is unchanged when the email address is unchanged', function (): void {
    $user = UserFactory::new()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'email' => $user->email,
            'name' => 'Test User',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('user can delete their account', function (): void {
    $user = UserFactory::new()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($user->fresh());
});

test('correct password must be provided to delete account', function (): void {
    $user = UserFactory::new()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('password')
        ->assertRedirect('/profile');

    $this->assertNotNull($user->fresh());
});
