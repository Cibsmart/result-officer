<?php

declare(strict_types=1);

use App\Data\Download\PortalDepartmentData;
use Illuminate\Support\Collection;

test('portal program data is correct', function (): void {
    $department = [
        'code' => 'CSC',
        'faculty' => 'PHYSICAL SCIENCE',
        'id' => '1',
        'name' => 'COMPUTER SCIENCE',
        'options' => [['id' => '1', 'name' => 'Program 1'], ['id' => '2', 'name' => 'Program 2']],
    ];

    $data = PortalDepartmentData::from($department);

    expect($data)->toBeInstanceOf(PortalDepartmentData::class)
        ->and($data->onlineId)->toBe($department['id'])
        ->and($data->departmentCode)->toBe($department['code'])
        ->and($data->departmentName)->toBe($department['name'])
        ->and($data->facultyName)->toBe($department['faculty'])
        ->and($data->programs)->toBeInstanceOf(Collection::class);
});
