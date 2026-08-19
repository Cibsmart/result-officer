<?php

declare(strict_types=1);

/**
 * Public registration is closed. Accounts are provisioned by an administrator.
 *
 * Self-registration created a live, logged-in account with no email-domain
 * restriction, no verification and no approval step — on a system holding the
 * student records of the whole institution.
 */
test('the registration screen is gone', function (): void {
    $this->get('/register')->assertNotFound();
});

test('an account cannot be created by posting to the registration endpoint', function (): void {
    $this->post('/register', [
        'email' => 'intruder@example.com',
        'name' => 'Intruder',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertNotFound();

    $this->assertGuest();
    $this->assertDatabaseMissing('users', ['email' => 'intruder@example.com']);
});

test('no route is named register', function (): void {
    expect(app('router')->getRoutes()->getByName('register'))->toBeNull();
});
