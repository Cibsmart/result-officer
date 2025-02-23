<?php

declare(strict_types=1);

use App\Actions\Students\Updates\EntryModelUpdateAction;
use App\Enums\EntryMode;
use App\Enums\ModifiableFields\StudentModifiableField;
use App\Enums\RecordActionType;
use App\Models\Student;
use App\Models\StudentHistory;
use Tests\Factories\StudentFactory;
use Tests\Factories\UserFactory;

use function Pest\Laravel\assertDatabaseHas;

covers(EntryModelUpdateAction::class);

it('updates student entry mode', function (): void {
    $student = StudentFactory::new()->createOne();

    $newValue = EntryMode::DENT;

    (new EntryModelUpdateAction())->execute($student, $newValue);

    $student->refresh();

    expect($student->entry_mode)->toBe($newValue);
});

it('records the update in the student history table', function (): void {
    $student = StudentFactory::new()->createOne();
    $user = UserFactory::new()->createOne();

    $oldValue = $student->entry_mode;
    $newValue = EntryMode::DENT;

    (new EntryModelUpdateAction())->execute($student, $newValue, user: $user);

    $student->refresh();

    assertDatabaseHas(StudentHistory::class, [
        'action' => RecordActionType::UPDATE,
        'data' => json_encode(['new' => $newValue, 'old' => $oldValue]),
        'field' => StudentModifiableField::ENTRY_MODE,
        'modifiable_id' => $student->id,
        'modifiable_type' => (new Student())->getMorphClass(),
        'student_id' => $student->id,
        'user_id' => $user->id,
    ]);
});
