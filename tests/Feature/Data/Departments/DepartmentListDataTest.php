<?php

declare(strict_types=1);

use App\Data\Department\DepartmentListData;
use Tests\Factories\DepartmentFactory;

test('department list dto is correct', function (): void {
    $count = 5;
    DepartmentFactory::new()->active()->count($count)->create();

    $data = DepartmentListData::new();

    expect($data)->toBeInstanceOf(DepartmentListData::class)
        ->and($data->departments->count())->toEqual($count);
});

test('department list dto contains only active department', function (): void {
    $count = 5;
    DepartmentFactory::new()->active()->count($count)->create();
    DepartmentFactory::new()->count($count)->create();

    $data = DepartmentListData::new();

    expect($data)->toBeInstanceOf(DepartmentListData::class)
        ->and($data->departments->count())->toEqual($count);
});
