<?php

declare(strict_types=1);

use Inertia\Testing\AssertableInertia as Assert;
use Tests\Factories\UserFactory;

use function Pest\Laravel\actingAs;

test('view result form loads', function (): void {
    $user = UserFactory::new()->create();

    actingAs($user)
        ->get(route('results.form'))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
            ->component('results/form/page'));
});

test('student result view page loads', function (): void {
    $user = UserFactory::new()->createOne();
    $student = createStudentWithResults();

    actingAs($user)
        ->from(route('results.form'))
        ->post(route('results.view', [
            'matriculation_number' => $student->matriculation_number,
        ]))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
            ->component('results/view/page')
            ->has('student')
            ->has('results'),
        );
});

test('matriculation number is required', function (): void {
    $user = UserFactory::new()->createOne();

    actingAs($user)
        ->from(route('results.form'))
        ->post(route('results.view'))
        ->assertSessionHasErrors(['matriculation_number' => 'The matriculation number field is required.']);
});

test('matriculation number length must be at 14 characters', function (): void {
    $user = UserFactory::new()->createOne();

    actingAs($user)
        ->from(route('results.form'))
        ->post(route('results.view', [
            'matriculation_number' => '2009/51486',
        ]))
        ->assertSessionHasErrors([
            'matriculation_number' => 'The matriculation number field must be at least 14 characters.',
        ]);
});

test('matriculation number must be valid', function (): void {
    $user = UserFactory::new()->createOne();

    actingAs($user)
        ->from(route('results.form'))
        ->post(route('results.view', [
            'matriculation_number' => 'EBUS/2009/51486',
        ]))
        ->assertSessionHasErrors(['matriculation_number' => 'The matriculation number field format is invalid.']);
});
