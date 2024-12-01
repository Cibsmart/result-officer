<?php

declare(strict_types=1);

use App\Data\Department\DepartmentListData;
use Tests\Factories\DepartmentFactory;

test('department list dto is correct', function (): void {
    $count = 5;
    DepartmentFactory::new()->active()->count($count)->create();

    $departments = DepartmentListData::new();

    expect($departments)->toBeInstanceOf(DepartmentListData::class)
        ->and($departments->data->count())->toEqual($count + 1);
});

test('department list dto contains only active department', function (): void {
    $count = 5;
    DepartmentFactory::new()->active()->count($count)->create();
    DepartmentFactory::new()->count($count)->create();

    $departments = DepartmentListData::new();

    expect($departments)->toBeInstanceOf(DepartmentListData::class)
        ->and($departments->data->count())->toEqual($count + 1);
});
