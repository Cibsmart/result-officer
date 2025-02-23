<?php

declare(strict_types=1);

use App\Actions\Students\Updates\JambRegistrationNumberUpdateAction;
use App\Enums\ModifiableFields\StudentModifiableField;
use App\Enums\RecordActionType;
use App\Models\Student;
use App\Models\StudentHistory;
use Tests\Factories\StudentFactory;
use Tests\Factories\UserFactory;

use function Pest\Laravel\assertDatabaseHas;

covers(JambRegistrationNumberUpdateAction::class);

it('updates student jamb registration number', function (): void {
    $student = StudentFactory::new()->createOne();

    $newValue = 'JAMB123456789';

    (new JambRegistrationNumberUpdateAction())->execute($student, $newValue);

    $student->refresh();

    expect($student->jamb_registration_number)->toBe($newValue);
});

it('records the update in the student history table', function (): void {
    $student = StudentFactory::new()->createOne();
    $user = UserFactory::new()->createOne();

    $oldValue = '';
    $newValue = 'JAMB123456789';

    (new JambRegistrationNumberUpdateAction())->execute($student, $newValue, user: $user);

    $student->refresh();

    assertDatabaseHas(StudentHistory::class, [
        'action' => RecordActionType::UPDATE,
        'data' => json_encode(['new' => $newValue, 'old' => $oldValue]),
        'field' => StudentModifiableField::JAMB_REGISTRATION_NUMBER,
        'modifiable_id' => $student->id,
        'modifiable_type' => (new Student())->getMorphClass(),
        'student_id' => $student->id,
        'user_id' => $user->id,
    ]);
});
