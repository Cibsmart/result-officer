<?php

declare(strict_types=1);

use Tests\Factories\UserFactory;

use function Pest\Laravel\actingAs;

it('loads the correct component', function (): void {
    $user = UserFactory::new()->createOne();

    actingAs($user)->get(route('students.show'))
        ->assertHasComponent('students/show/page');
});
