<?php

declare(strict_types=1);

use App\Data\Students\StudentComprehensiveData;
use Tests\Factories\StudentFactory;
use Tests\Factories\UserFactory;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('loads the correct component', function (): void {
    $user = UserFactory::new()->createOne();

    actingAs($user)->get(route('students.show'))
        ->assertHasComponent('students/show/page');
});

it('redirects guest to login', function (): void {
    get(route('students.show'))->assertRedirect('login');
});

it('passes student data to the view when student param is present', function (): void {
    $user = UserFactory::new()->createOne();
    $student = StudentFactory::new()->createOne();

    actingAs($user)->get(route('students.show', $student))
        ->assertHasData('data', StudentComprehensiveData::fromModel($student));
});
