<?php

declare(strict_types=1);

use App\Actions\Students\StudentDeleteAction;
use App\Enums\ModifiableFields\StudentModifiableField;
use App\Enums\RecordActionType;
use App\Models\Student;
use App\Models\StudentHistory;
use Tests\Factories\StudentFactory;
use Tests\Factories\UserFactory;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertSoftDeleted;

it('soft deletes student', function (): void {
    $student = StudentFactory::new()->createOne();

    (new StudentDeleteAction())->execute($student);

    assertSoftDeleted($student);
});

it('throws exception when trying to delete graduated student', function (): void {
    $student = StudentFactory::new()->graduated()->createOne();

    (new StudentDeleteAction())->execute($student);
})->throws(Exception::class);

it('throws exception when trying to delete cleared student', function (): void {
    $student = StudentFactory::new()->cleared()->createOne();

    (new StudentDeleteAction())->execute($student);
})->throws(Exception::class);

it('records the delete in the student history table', function (): void {
    $student = StudentFactory::new()->createOne();

    $user = UserFactory::new()->createOne();

    $oldValue = $student->registration_number;

    (new StudentDeleteAction())->execute($student, user: $user);

    $student->refresh();

    assertDatabaseHas(StudentHistory::class, [
        'action' => RecordActionType::DELETE,
        'data' => json_encode(['old' => $oldValue]),
        'field' => StudentModifiableField::REGISTRATION_NUMBER,
        'modifiable_id' => $student->id,
        'modifiable_type' => (new Student())->getMorphClass(),
        'student_id' => $student->id,
        'user_id' => $user->id,
    ]);
});
