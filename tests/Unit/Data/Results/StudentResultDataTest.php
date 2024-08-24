<?php

declare(strict_types=1);

use App\Data\Results\StudentResultData;
use App\Enums\ClassOfDegree;
use Tests\Factories\StudentFactory;

test('student result data is correct', function (): void {
    $student = createStudentWithResults();

    $lastYear = $student->enrollments->last()->session->lastYear();

    $resultData = StudentResultData::from($student);
    $programType = $student->program->programType;

    $fcgpa = computeFCGPA($student);
    $degreeClass = ClassOfDegree::for($fcgpa);

    expect($resultData)->toBeInstanceOf(StudentResultData::class)
        ->and($resultData->id)->toBe($student->id)
        ->and($resultData->enrollments->count())->toBe($student->enrollments->count())
        ->and($resultData->finalCumulativeGradePointAverage)->toBe($fcgpa)
        ->and($resultData->degreeClass)->toBe($degreeClass->value)
        ->and($resultData->degreeAwarded)->toBe("$programType->name ($programType->code)")
        ->and($resultData->graduationYear)->toBe($lastYear);
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
