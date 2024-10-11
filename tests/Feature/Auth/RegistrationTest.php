<?php

declare(strict_types=1);

use App\Enums\Role;

test('registration screen can be rendered', function (): void {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function (): void {
    $response = $this->post('/register', [
        'email' => 'test@example.com',
        'name' => 'Test User',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => Role::USER->value,
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});
