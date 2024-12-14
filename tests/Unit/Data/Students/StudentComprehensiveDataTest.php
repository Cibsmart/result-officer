<?php

declare(strict_types=1);

use App\Data\Results\StudentResultData;
use App\Data\Students\StudentComprehensiveData;
use App\Data\Students\StudentData;

covers(StudentComprehensiveData::class);

it('returns correct data object', function (): void {
    $student = createStudentWithResults();

    $data = StudentComprehensiveData::fromModel($student);

    expect($data)->toBeInstanceOf(StudentComprehensiveData::class)
        ->toHaveProperties(['student', 'results'])
        ->and($data->student)->toBeInstanceOf(StudentData::class)
        ->and($data->results)->toBeInstanceOf(StudentResultData::class);
});
