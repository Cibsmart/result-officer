<?php

declare(strict_types=1);

use App\Data\Students\StudentBasicData;
use Tests\Factories\StudentFactory;

test('student data object is correct', function (): void {
    $student = StudentFactory::new()->createOne();

    $studentData = StudentBasicData::from($student);

    expect($studentData)->toBeInstanceOf(StudentBasicData::class)
        ->and($studentData->registrationNumber)->toBe($student->registration_number)
        ->and($studentData->lastName)->toBe($student->last_name)
        ->and($studentData->firstName)->toBe($student->first_name)
        ->and($studentData->gender)->toBe($student->gender)
        ->and($studentData->birthDate)->toBe($student->date_of_birth->format('jS M, Y'))
        ->and($studentData->program)->toBe($student->program->name)
        ->and($studentData->department)->toBe($student->program->department->name)
        ->and($studentData->faculty)->toBe($student->program->department->faculty->name)
        ->and($studentData->admissionYear)->toBe($student->entrySession->firstYear())
        ->and($studentData->nationality)->toBe($student->lga->state->country->demonym);
});

it('returns blank date for student without date', function (): void {
    $student = StudentFactory::new()->createOne(['date_of_birth' => null]);

    $studentData = StudentBasicData::from($student);

    expect($studentData)->toBeInstanceOf(StudentBasicData::class)
        ->and($studentData->birthDate)->toBe('');
});
