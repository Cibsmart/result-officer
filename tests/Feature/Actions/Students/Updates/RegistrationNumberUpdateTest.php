<?php

declare(strict_types=1);

use App\Actions\Students\Updates\RegistrationNumberUpdate;
use App\Enums\ModifiableFields\StudentModifiableField;
use App\Enums\RecordActionType;
use App\Models\Student;
use App\Models\StudentHistory;
use Illuminate\Support\Str;
use Tests\Factories\StudentFactory;

use function Pest\Laravel\assertDatabaseHas;

covers(RegistrationNumberUpdate::class);

it('updates student registration number', function (): void {
    $student = StudentFactory::new()->createOne();

    $newRegistrationNumber = 'EBSU/2009/51486';

    RegistrationNumberUpdate::execute($student, $newRegistrationNumber);

    $student->refresh();

    expect($student->registration_number)->toBe($newRegistrationNumber);
});

it('correctly updates student slug along with the registration number', function (): void {
    $student = StudentFactory::new()->createOne();

    $newRegistrationNumber = 'EBSU/2009/51486';

    RegistrationNumberUpdate::execute($student, $newRegistrationNumber);

    $student->refresh();

    expect($student->slug)->toBe(Str::of($newRegistrationNumber)->replace('/', '-')->slug()->value());
});

it('throws exception for invalid registration number', function (): void {
    $student = StudentFactory::new()->createOne();

    $newRegistrationNumber = 'EBSU/209/51486';

    RegistrationNumberUpdate::execute($student, $newRegistrationNumber);
})->throws(InvalidArgumentException::class, 'Invalid registration number');

it('records the update in the student history table', function (): void {
    $student = StudentFactory::new()->createOne();

    $oldRegistrationNumber = $student->registration_number;
    $newRegistrationNumber = 'EBSU/2009/51486';

    RegistrationNumberUpdate::execute($student, $newRegistrationNumber);

    $student->refresh();

    assertDatabaseHas(StudentHistory::class, [
        'action' => RecordActionType::UPDATE,
        'data' => json_encode(['new' => $newRegistrationNumber, 'old' => $oldRegistrationNumber]),
        'field' => StudentModifiableField::REGISTRATION_NUMBER,
        'modifiable_id' => $student->id,
        'modifiable_type' => (new Student())->getMorphClass(),
        'student_id' => $student->id,
    ]);
});
