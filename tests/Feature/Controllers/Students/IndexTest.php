<?php

declare(strict_types=1);

use Tests\Factories\UserFactory;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('redirects guest to login', function (): void {

    get(route('students.index'))->assertRedirect('login');
});

it('loads the correct component', function (): void {
    $user = UserFactory::new()->createOne();

    actingAs($user)->get(route('students.index'))
        ->assertHasComponent('students/index/page');
});
