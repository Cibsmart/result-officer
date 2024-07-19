<?php

declare(strict_types=1);

use App\Data\Results\StudentResultData;
use App\Services\DegreeClass;
use App\Services\RetrieveYear;

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
