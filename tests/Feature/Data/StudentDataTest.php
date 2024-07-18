<?php

declare(strict_types=1);

use App\Data\Students\StudentData;
use Tests\Factories\StudentFactory;

test('student data object is correct', function (): void {
    $student = StudentFactory::new()->createOne();

    $studentData = StudentData::from($student);

    expect($studentData)->toBeInstanceOf(StudentData::class)
        ->and($studentData->matriculationNumber)->toBe($student->matriculation_number)
        ->and($studentData->lastName)->toBe($student->last_name)
        ->and($studentData->firstName)->toBe($student->first_name)
        ->and($studentData->gender)->toBe($student->gender)
        ->and($studentData->birthDate)->toBe($student->date_of_birth->format('d/m/Y'))
        ->and($studentData->program)->toBe($student->program->name)
        ->and($studentData->department)->toBe($student->program->department->name)
        ->and($studentData->faculty)->toBe($student->program->department->faculty->name)
        ->and($studentData->admissionYear)->toBe($student->entrySession->name)
        ->and($studentData->nationality)->toBe($student->country->demonym);
});
