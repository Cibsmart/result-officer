<?php

declare(strict_types=1);

use App\Data\Course\CourseListData;
use Tests\Factories\CourseFactory;

test('course list dto is correct', function (): void {
    $count = 4;

    CourseFactory::new()->count($count)->create();

    $data = CourseListData::new();

    expect($data)->toBeInstanceOf(CourseListData::class)
        ->and($data->courses->count())->toEqual($count + 1);
});

test('course list dto is correct with search term', function (): void {
    CourseFactory::new()
        ->sequence(
            ['code' => 'CSC 101'],
            ['code' => 'CSC 102'],
            ['code' => 'ACC 101'],
            ['code' => 'BED 101'])
        ->count(4)
        ->create();

    $data = CourseListData::new('CSC');

    expect($data)->toBeInstanceOf(CourseListData::class)
        ->and($data->courses->count())->toEqual(3);
});
