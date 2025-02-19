<?php

declare(strict_types=1);

use App\Actions\Students\Updates\StudentStatusUpdate;
use App\Enums\ModifiableFields\StudentModifiableField;
use App\Enums\RecordActionType;
use App\Enums\StudentStatus;
use App\Models\Student;
use App\Models\StudentHistory;
use Tests\Factories\StudentFactory;
use Tests\Factories\UserFactory;

use function Pest\Laravel\assertDatabaseHas;

it('can update student status', function (): void {
    $student = StudentFactory::new()->createOne();
    $newStatus = StudentStatus::ACTIVE;

    (new StudentStatusUpdate())->execute($student, $newStatus);

    $student->fresh();

    expect($student->status)->toBe($newStatus);
});

it('cannot update status of graduated student', function (): void {
    $student = StudentFactory::new()->graduated()->createOne();

    (new StudentStatusUpdate())->execute($student, StudentStatus::ACTIVE);

    $student->fresh();

    expect($student->status)->toBe(StudentStatus::GRADUATED);
});

it('records the update in the student history table', function (): void {
    $student = StudentFactory::new()->createOne();
    $user = UserFactory::new()->createOne();

    $oldStatus = $student->status;
    $newStatus = StudentStatus::FINAL_YEAR;

    (new StudentStatusUpdate())->execute($student, $newStatus, user: $user);

    $student->refresh();

    assertDatabaseHas(StudentHistory::class, [
        'action' => RecordActionType::UPDATE,
        'data' => json_encode(['new' => $newStatus, 'old' => $oldStatus]),
        'field' => StudentModifiableField::STATUS,
        'modifiable_id' => $student->id,
        'modifiable_type' => (new Student())->getMorphClass(),
        'student_id' => $student->id,
        'user_id' => $user->id,
    ]);
});
