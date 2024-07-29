<?php

declare(strict_types=1);

use App\Data\Department\DepartmentData;
use App\Data\Level\LevelData;
use App\Data\Session\SessionData;
use App\Data\Summary\DepartmentResultSummaryData;

test('department summary data is correct', function (): void {
    $studentCount = 2;
    $students = createMultipleStudentsWithResults($studentCount, 1);

    $firstStudent = $students[0];

    $department = $firstStudent->program->department;
    $session = $firstStudent->entrySession;
    $level = $firstStudent->entryLevel;

    $data = DepartmentResultSummaryData::from($department, $session, $level);

    expect($data)->toBeInstanceOf(DepartmentResultSummaryData::class)
        ->and($data->department)->toBeInstanceOf(DepartmentData::class)
        ->and($data->session)->toBeInstanceOf(SessionData::class)
        ->and($data->level)->toBeInstanceOf(LevelData::class)
        ->and($data->students->count())->toEqual($studentCount);
});
