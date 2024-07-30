<?php

declare(strict_types=1);

use App\Data\Results\StudentResultData;
use App\Services\DegreeClass;
use App\Services\RetrieveYear;
use Tests\Factories\StudentFactory;

test('student result data is correct', function (): void {
    $student = createStudentWithResults();

    $lastSession = $student->enrollments->last()->session->name;

    $resultData = StudentResultData::from($student);
    $programType = $student->program->programType;

    $fcgpa = computeFCGPA($student);
    $degreeClass = DegreeClass::for($fcgpa)->value();

    expect($resultData)->toBeInstanceOf(StudentResultData::class)
        ->and($resultData->id)->toBe($student->id)
        ->and($resultData->enrollments->count())->toBe($student->enrollments->count())
        ->and($resultData->finalCumulativeGradePointAverage)->toBe($fcgpa)
        ->and($resultData->degreeClass)->toBe($degreeClass->value)
        ->and($resultData->degreeAwarded)->toBe("$programType->name ($programType->code)")
        ->and($resultData->graduationYear)->toBe(RetrieveYear::fromSession($lastSession)->lastYear());
});

test('student without enrollment return an empty state with zero fcgpa', function (): void {
    $student = StudentFactory::new()->create();

    $resultData = StudentResultData::from($student);

    expect($resultData)->toBeInstanceOf(StudentResultData::class)
        ->and($resultData->id)->toBe($student->id)
        ->and($resultData->enrollments->count())->toBe($student->enrollments->count())
        ->and($resultData->finalCumulativeGradePointAverage)->toBe(0.0)
        ->and($resultData->graduationYear)->toBe(0);
});
