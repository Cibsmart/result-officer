<?php

declare(strict_types=1);

use App\Actions\Students\Updates\BirthDateUpdateAction;
use App\Enums\ModifiableFields\StudentModifiableField;
use App\Enums\RecordActionType;
use App\Models\Student;
use App\Models\StudentHistory;
use App\Values\DateValue;
use Tests\Factories\StudentFactory;
use Tests\Factories\UserFactory;

use function Pest\Laravel\assertDatabaseHas;

covers(BirthDateUpdateAction::class);

it('updates student date of birth', function (): void {
    $student = StudentFactory::new()->createOne();

    $newValue = DateValue::fromValue(now()->subYears(18));

    (new BirthDateUpdateAction())->execute($student, $newValue);

    $student->refresh();

    expect($student->date_of_birth->format('Y-m-d'))->toBe($newValue->value->format('Y-m-d'));
});

it('records the update in the student history table', function (): void {
    $student = StudentFactory::new()->createOne();
    $user = UserFactory::new()->createOne();

    $oldValue = $student->date_of_birth;
    $newValue = DateValue::fromValue(now()->subYears(18));

    (new BirthDateUpdateAction())->execute($student, $newValue, user: $user);

    $student->refresh();

    assertDatabaseHas(StudentHistory::class, [
        'action' => RecordActionType::UPDATE,
        'data' => json_encode(['new' => $newValue->toString('Y-m-d'), 'old' => $oldValue->format('Y-m-d')]),
        'field' => StudentModifiableField::DATE_OF_BIRTH,
        'modifiable_id' => $student->id,
        'modifiable_type' => (new Student())->getMorphClass(),
        'student_id' => $student->id,
        'user_id' => $user->id,
    ]);
});
