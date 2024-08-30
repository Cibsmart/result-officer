<?php

declare(strict_types=1);

use App\Data\Composite\CompositeCourseData;

test('composite course data is correct', function (): void {
    $course = [
        'code' => 'CSC 101',
        'grade' => 'F',
        'score' => '30',
    ];

    $data = CompositeCourseData::fromArray($course);

    expect($data)->toBeInstanceOf(CompositeCourseData::class)
        ->and($data->code)->toBeString('CSC 101')
        ->and($data->grade)->toBeString('F')
        ->and($data->score)->toBeString('30');
});
