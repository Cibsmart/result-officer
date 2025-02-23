<?php

declare(strict_types=1);

use App\Actions\Students\Updates\ProgramUpdateAction;
use App\Enums\ModifiableFields\StudentModifiableField;
use App\Enums\RecordActionType;
use App\Models\Student;
use App\Models\StudentHistory;
use Tests\Factories\ProgramFactory;
use Tests\Factories\StudentFactory;
use Tests\Factories\UserFactory;

use function Pest\Laravel\assertDatabaseHas;

covers(ProgramUpdateAction::class);

it('updates student program', function (): void {
    $student = StudentFactory::new()->createOne();
    $newValue = ProgramFactory::new()->createOne(['name' => 'NEW PROGRAM']);

    (new ProgramUpdateAction())->execute($student, $newValue);

    $student->refresh();

    expect($student->program_id)->toBe($newValue->id);
});

it('records the update in the student history table', function (): void {
    $student = StudentFactory::new()->createOne();
    $user = UserFactory::new()->createOne();

    $oldValue = $student->program;
    $newValue = ProgramFactory::new()->createOne(['name' => 'NEW PROGRAM']);

    (new ProgramUpdateAction())->execute($student, $newValue, user: $user);

    $student->refresh();

    assertDatabaseHas(StudentHistory::class, [
        'action' => RecordActionType::UPDATE,
        'data' => json_encode(['new' => $newValue->name, 'old' => $oldValue->name]),
        'field' => StudentModifiableField::PROGRAM,
        'modifiable_id' => $student->id,
        'modifiable_type' => (new Student())->getMorphClass(),
        'student_id' => $student->id,
        'user_id' => $user->id,
    ]);
});
