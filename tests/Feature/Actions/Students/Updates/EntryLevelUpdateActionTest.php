<?php

declare(strict_types=1);

use App\Actions\Students\Updates\EntryLevelUpdateAction;
use App\Enums\ModifiableFields\StudentModifiableField;
use App\Enums\RecordActionType;
use App\Models\Student;
use App\Models\StudentHistory;
use Tests\Factories\LevelFactory;
use Tests\Factories\StudentFactory;
use Tests\Factories\UserFactory;

use function Pest\Laravel\assertDatabaseHas;

covers(EntryLevelUpdateAction::class);

it('updates student entry level', function (): void {
    $entryLevel = LevelFactory::new()->createOne(['name' => '100']);
    $student = StudentFactory::new()->createOne(['entry_level_id' => $entryLevel->id]);

    $newValue = LevelFactory::new()->createOne(['name' => '200']);

    (new EntryLevelUpdateAction())->execute($student, $newValue);

    $student->refresh();

    expect($student->entry_level_id)->toBe($newValue->id);
});

it('records the update in the student history table', function (): void {
    $entryLevel = LevelFactory::new()->createOne(['name' => '100']);
    $student = StudentFactory::new()->createOne(['entry_level_id' => $entryLevel->id]);
    $user = UserFactory::new()->createOne();

    $oldValue = $student->entryLevel;
    $newValue = LevelFactory::new()->createOne(['name' => '200']);

    (new EntryLevelUpdateAction())->execute($student, $newValue, user: $user);

    $student->refresh();

    assertDatabaseHas(StudentHistory::class, [
        'action' => RecordActionType::UPDATE,
        'data' => json_encode(['new' => $newValue->name, 'old' => $oldValue->name]),
        'field' => StudentModifiableField::ENTRY_LEVEL,
        'modifiable_id' => $student->id,
        'modifiable_type' => (new Student())->getMorphClass(),
        'student_id' => $student->id,
        'user_id' => $user->id,
    ]);
});
