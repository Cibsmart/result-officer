<?php

declare(strict_types=1);

use App\Data\Students\StudentBasicData;
use App\Data\Summary\StudentResultSummaryData;
use App\Models\Registration;

test('student result summary data is correct', function (): void {
    $student = createStudentWithResults();

    $data = StudentResultSummaryData::from($student);

    $fcgpa = computeFCGPA($student);

    expect($data)->toBeInstanceOf(StudentResultSummaryData::class)
        ->and($data->student)->toBeInstanceOf(StudentBasicData::class)
        ->and($data->fcgpa)->toEqual($fcgpa)
        ->and($data->resultsCount)->toEqual(Registration::count());
});
