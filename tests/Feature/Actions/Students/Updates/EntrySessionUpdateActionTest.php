<?php

declare(strict_types=1);

use App\Actions\Students\Updates\EntrySessionUpdateAction;
use App\Enums\ModifiableFields\StudentModifiableField;
use App\Enums\RecordActionType;
use App\Models\Student;
use App\Models\StudentHistory;
use Tests\Factories\SessionFactory;
use Tests\Factories\StudentFactory;
use Tests\Factories\UserFactory;

use function Pest\Laravel\assertDatabaseHas;

covers(EntrySessionUpdateAction::class);

it('updates student entry session', function (): void {
    $entrySession = SessionFactory::new()->createOne(['name' => '2009/2010']);
    $student = StudentFactory::new()->createOne(['entry_session_id' => $entrySession->id]);
    $newValue = SessionFactory::new()->createOne(['name' => '2010/2011']);

    (new EntrySessionUpdateAction())->execute($student, $newValue);

    $student->refresh();

    expect($student->entry_session_id)->toBe($newValue->id);
});

it('records the update in the student history table', function (): void {
    $entrySession = SessionFactory::new()->createOne(['name' => '2009/2010']);
    $student = StudentFactory::new()->createOne(['entry_session_id' => $entrySession->id]);
    $user = UserFactory::new()->createOne();

    $oldValue = $student->entrySession;
    $newValue = SessionFactory::new()->createOne(['name' => '2010/2011']);

    (new EntrySessionUpdateAction())->execute($student, $newValue, user: $user);

    $student->refresh();

    assertDatabaseHas(StudentHistory::class, [
        'action' => RecordActionType::UPDATE,
        'data' => json_encode(['new' => $newValue->name, 'old' => $oldValue->name]),
        'field' => StudentModifiableField::ENTRY_SESSION,
        'modifiable_id' => $student->id,
        'modifiable_type' => (new Student())->getMorphClass(),
        'student_id' => $student->id,
        'user_id' => $user->id,
    ]);
});
