<?php

declare(strict_types=1);

use Inertia\Testing\AssertableInertia as Assert;
use Tests\Factories\UserFactory;

use function Pest\Laravel\actingAs;

it('loads the result index page', function (): void {
    $user = UserFactory::new()->create();

    actingAs($user)
        ->get(route('results.index'))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
            ->component('results/index/page'));
});

it('redirects from student result form to the index page', function (): void {
    $user = UserFactory::new()->createOne();
    $student = createStudentWithResults();

    actingAs($user)
        ->from(route('results.index'))
        ->post(route('results.store', [
            'registration_number' => $student->registration_number,
        ]))
        ->assertStatus(302)
        ->assertRedirect(route('results.index', ['student' => $student]));
});

it('sends student and result data to the view', function (): void {
    $user = UserFactory::new()->createOne();
    $student = createStudentWithResults();

    actingAs($user)
        ->get(route('results.index', ['student' => $student]))
        ->assertInertia(fn (Assert $page) => $page
            ->component('results/index/page')
            ->has('student')
            ->has('results'),
        );
});

it('validates registration number required', function (): void {
    $user = UserFactory::new()->createOne();

    actingAs($user)
        ->from(route('results.index'))
        ->post(route('results.store'))
        ->assertSessionHasErrors(['registration_number' => 'The registration number field is required.']);
});

it('validates registration number length', function (): void {
    $user = UserFactory::new()->createOne();

    actingAs($user)
        ->from(route('results.index'))
        ->post(route('results.store', [
            'registration_number' => '2009/51486',
        ]))
        ->assertSessionHasErrors([
            'registration_number' => 'The registration number field must be at least 14 characters.',
        ]);
});

it('validates registration number valid', function (): void {
    $user = UserFactory::new()->createOne();

    actingAs($user)
        ->from(route('results.index'))
        ->post(route('results.store', [
            'registration_number' => 'EBUS/2009/51486',
        ]))
        ->assertSessionHasErrors(['registration_number' => 'The registration number field format is invalid.']);
});
