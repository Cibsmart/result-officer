<?php

declare(strict_types=1);

use App\Data\Department\DepartmentData;
use Tests\Factories\DepartmentFactory;

test('department dto is correct', function (): void {
    $department = DepartmentFactory::new()->createOne();

    $data = DepartmentData::from($department);

    expect($data)->toBeInstanceOf(DepartmentData::class)
        ->and($data->id)->toBe($department->id)
        ->and($data->name)->toBe($department->name);
});
