<?php

declare(strict_types=1);

use App\Actions\Students\Updates\RegistrationNumberUpdate;
use App\Enums\ModifiableFields\StudentModifiableField;
use App\Enums\RecordActionType;
use App\Models\Student;
use App\Models\StudentHistory;
use App\Values\RegistrationNumber;
use Tests\Factories\StudentFactory;
use Tests\Factories\UserFactory;

use function Pest\Laravel\assertDatabaseHas;

covers(RegistrationNumberUpdate::class);

it('updates student registration number', function (): void {
    $student = StudentFactory::new()->createOne();

    $newRegistrationNumber = RegistrationNumber::new('EBSU/2009/51486');

    (new RegistrationNumberUpdate())->execute($student, $newRegistrationNumber);

    $student->refresh();

    expect($student->registration_number)->toBe($newRegistrationNumber->value);
});

it('updates student slug along with the registration number', function (): void {
    $student = StudentFactory::new()->createOne();

    $newRegistrationNumber = RegistrationNumber::new('EBSU/2009/51486');

    (new RegistrationNumberUpdate())->execute($student, $newRegistrationNumber);

    $student->refresh();

    expect($student->slug)->toBe('ebsu-2009-51486');
});

it('updates student number along with the registration number', function (): void {
    $student = StudentFactory::new()->createOne();

    $newRegistrationNumber = RegistrationNumber::new('EBSU/2009/51486');

    (new RegistrationNumberUpdate())->execute($student, $newRegistrationNumber);

    $student->refresh();

    expect($student->number)->toBe('51486');
});

it('records the update in the student history table', function (): void {
    $student = StudentFactory::new()->createOne();
    $user = UserFactory::new()->createOne();

    $oldRegistrationNumber = $student->registration_number;
    $newRegistrationNumber = RegistrationNumber::new('EBSU/2009/51486');

    (new RegistrationNumberUpdate())->execute($student, $newRegistrationNumber, user: $user);

    $student->refresh();

    assertDatabaseHas(StudentHistory::class, [
        'action' => RecordActionType::UPDATE,
        'data' => json_encode(['new' => $newRegistrationNumber->value, 'old' => $oldRegistrationNumber]),
        'field' => StudentModifiableField::REGISTRATION_NUMBER,
        'modifiable_id' => $student->id,
        'modifiable_type' => (new Student())->getMorphClass(),
        'student_id' => $student->id,
        'user_id' => $user->id,
    ]);
});
