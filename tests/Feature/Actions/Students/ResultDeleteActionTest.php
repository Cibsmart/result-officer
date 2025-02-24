<?php

declare(strict_types=1);

use App\Actions\Results\ResultDeleteAction;
use App\Enums\ModifiableFields\StudentModifiableField;
use App\Enums\RecordActionType;
use App\Enums\StudentStatus;
use App\Models\Registration;
use App\Models\StudentHistory;
use Tests\Factories\UserFactory;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertSoftDeleted;

it('soft deletes student registration', function (): void {
    $student = createStudentWithResults(1, 1, 1);
    $registration = Registration::first();

    (new ResultDeleteAction())->execute($student, $registration);

    assertSoftDeleted($registration);
});

it('throws exception when trying to delete graduated student result', function (): void {
    $student = createStudentWithResults(1, 1, 1, ['status' => StudentStatus::GRADUATED]);
    $registration = Registration::first();

    (new ResultDeleteAction())->execute($student, $registration);
})->throws(Exception::class);

it('throws exception when trying to delete cleared student result', function (): void {
    $student = createStudentWithResults(1, 1, 1, ['status' => StudentStatus::CLEARED]);
    $registration = Registration::first();

    (new ResultDeleteAction())->execute($student, $registration);
})->throws(Exception::class);

it('records the delete in the student history table', function (): void {
    $student = createStudentWithResults(1, 1, 1);
    $registration = Registration::first();

    $user = UserFactory::new()->createOne();

    $oldValue = $registration->getUpdateData();

    (new ResultDeleteAction())->execute($student, $registration, user: $user);

    $student->refresh();

    assertDatabaseHas(StudentHistory::class, [
        'action' => RecordActionType::DELETE,
        'data' => json_encode(['old' => $oldValue]),
        'field' => StudentModifiableField::RESULT,
        'modifiable_id' => $registration->id,
        'modifiable_type' => (new Registration())->getMorphClass(),
        'student_id' => $student->id,
        'user_id' => $user->id,
    ]);
});
