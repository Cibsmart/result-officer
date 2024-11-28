<?php

declare(strict_types=1);

use App\Enums\StudentStatus;
use App\Models\Student;
use Tests\Factories\StudentFactory;
use Tests\Factories\UserFactory;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

it('can clear a student', function (): void {
    $user = UserFactory::new()->createOne();

    $student = StudentFactory::new()->createOne(['status' => StudentStatus::VETTED]);

    actingAs($user)->post(route('students.clearance.store', $student));

    assertDatabaseHas(Student::class, [
        'id' => $student->id,
        'status' => StudentStatus::CLEARED,
    ]);
});

it('redirects back to the vetting page', function (): void {
    $user = UserFactory::new()->createOne();

    $student = StudentFactory::new()->createOne(['status' => StudentStatus::VETTED]);

    actingAs($user)->fromRoute('vetting.index')
        ->post(route('students.clearance.store', $student))
        ->assertRedirect(route('vetting.index', $student->department()));
});

it('redirect guest to login', function (): void {
    $user = UserFactory::new()->createOne();

    $student = StudentFactory::new()->createOne(['status' => StudentStatus::VETTED]);

    actingAs($user)->fromRoute('vetting.index')
        ->post(route('students.clearance.store', $student))
        ->assertRedirect(route('vetting.index'));
});
