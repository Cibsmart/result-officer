<?php

declare(strict_types=1);

use App\Data\Results\StudentResultData;

test('student result data is correct', function (): void {
    $student = createStudentWithResults();

    $resultData = StudentResultData::from($student);

    $fcgpa = computeFCGPA($student);

    expect($resultData)->toBeInstanceOf(StudentResultData::class)
        ->and($resultData->id)->toBe($student->id)
        ->and($resultData->enrollments->count())->toBe($student->enrollments->count())
        ->and($resultData->finalCumulativeGradePointAverage)->toBe($fcgpa);
});
