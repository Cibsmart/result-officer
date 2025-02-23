<?php

declare(strict_types=1);

use App\Actions\Students\Updates\LocalGovernmentUpdateAction;
use App\Enums\ModifiableFields\StudentModifiableField;
use App\Enums\RecordActionType;
use App\Models\Student;
use App\Models\StudentHistory;
use Tests\Factories\LocalGovernmentFactory;
use Tests\Factories\StudentFactory;
use Tests\Factories\UserFactory;

use function Pest\Laravel\assertDatabaseHas;

covers(LocalGovernmentUpdateAction::class);

it('updates student local government', function (): void {
    $student = StudentFactory::new()->createOne();

    $newValue = LocalGovernmentFactory::new()->createOne(['name' => 'NEW LOCAL GOVERNMENT']);

    (new LocalGovernmentUpdateAction())->execute($student, $newValue);

    $student->refresh();

    expect($student->local_government_id)->toBe($newValue->id);
});

it('records the update in the student history table', function (): void {
    $student = StudentFactory::new()->createOne();
    $user = UserFactory::new()->createOne();

    $oldValue = $student->lga;
    $newValue = LocalGovernmentFactory::new()->createOne(['name' => 'NEW LOCAL GOVERNMENT']);

    (new LocalGovernmentUpdateAction())->execute($student, $newValue, user: $user);

    $student->refresh();

    assertDatabaseHas(StudentHistory::class, [
        'action' => RecordActionType::UPDATE,
        'data' => json_encode(['new' => $newValue->name, 'old' => $oldValue->name]),
        'field' => StudentModifiableField::LOCAL_GOVERNMENT,
        'modifiable_id' => $student->id,
        'modifiable_type' => (new Student())->getMorphClass(),
        'student_id' => $student->id,
        'user_id' => $user->id,
    ]);
});
