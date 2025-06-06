<?php

declare(strict_types=1);

use App\Enums\StudentStatus;
use App\Enums\VettingEventStatus;
use App\Models\Student;
use Tests\Factories\ExamOfficerFactory;
use Tests\Factories\StudentFactory;
use Tests\Factories\UserFactory;
use Tests\Factories\VettingEventFactory;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;

it('can clear a student', function (): void {
    $user = UserFactory::new()->createOne();

    $student = StudentFactory::new()->createOne(['status' => StudentStatus::FINAL_YEAR]);
    VettingEventFactory::new()->for($student)->createOne(['status' => VettingEventStatus::PASSED]);
    $examOfficer = ExamOfficerFactory::new()->createOne();

    actingAs($user)->post(route('students.clearance.store', $student), [
        'exam_officer' => $examOfficer->id,
        'month' => now()->monthName,
        'year' => now()->year,
    ]);

    assertDatabaseHas(Student::class, [
        'id' => $student->id,
        'status' => StudentStatus::CLEARED,
    ]);
});

it('redirects back to the vetting page', function (): void {
    $user = UserFactory::new()->createOne();

    $student = StudentFactory::new()->createOne(['status' => StudentStatus::FINAL_YEAR]);
    VettingEventFactory::new()->for($student)->createOne(['status' => VettingEventStatus::PASSED]);
    $examOfficer = ExamOfficerFactory::new()->createOne();

    actingAs($user)->fromRoute('graduand.index')
        ->post(route('students.clearance.store', $student), [
            'exam_officer' => $examOfficer->id,
            'month' => now()->monthName,
            'year' => now()->year,
        ])
        ->assertRedirect(route('graduand.index', $student->department()));
});

it('redirect guest to login', function (): void {
    $student = StudentFactory::new()->createOne();

    post(route('students.clearance.store', $student))
        ->assertRedirect(route('login'));
});
