<?php

declare(strict_types=1);

use App\Data\Composite\CompositeCourseData;
use App\Data\Composite\CompositeRowData;
use Illuminate\Support\Collection;

test('composite row data is correct', function (): void {
    $row = [
        'creditUnitTotal' => 0,
        'gradePointAverage' => '0.000',
        'gradePointTotal' => 0,
        'id' => 1,
        'levelCourses' => CompositeCourseData::collect(collect([])),
        'name' => 'Student Name',
        'otherCourses' => '',
        'registrationNumber' => 'RegistrationNumber',
        'remark' => 'PASS',
    ];

    $data = CompositeRowData::fromArray($row);

    expect($data)->toBeInstanceOf(CompositeRowData::class)
        ->and($data->studentId)->toBeInt()->toBe($row['id'])
        ->and($data->studentName)->toBeString()->toBe($row['name'])
        ->and($data->registrationNumber)->toBeString()->toBe($row['registrationNumber'])
        ->and($data->creditUnitTotal)->toBeString()->toBe((string) $row['creditUnitTotal'])
        ->and($data->gradePointTotal)->toBeString()->toBe((string) $row['gradePointTotal'])
        ->and($data->gradePointAverage)->toBeString()->toBe($row['gradePointAverage'])
        ->and($data->levelCourses)->toBeInstanceOf(Collection::class)
        ->toContainOnlyInstancesOf(CompositeCourseData::class)
        ->and($data->otherCourses)->toBeString()->toBeEmpty()
        ->and($data->remark)->toBeString()->toBe('PASS');
});
