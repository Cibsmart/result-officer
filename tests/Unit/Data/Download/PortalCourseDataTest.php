<?php

declare(strict_types=1);

use App\Data\Download\PortalCourseData;

test('portal program data is correct', function (): void {
    $course = ['id' => '1', 'course_code' => 'CSC 101', 'course_title' => 'INTRODUCTION TO COMPUTER SCIENCE'];

    $data = PortalCourseData::from($course);

    expect($data)->toBeInstanceOf(PortalCourseData::class)
        ->and($data->onlineId)->toBe($course['id'])
        ->and($data->code)->toBe($course['course_code'])
        ->and($data->title)->toBe($course['course_title']);
});
