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

it('works with uppercase array keys', function (): void {
    $course = ['ID' => '1', 'COURSE_CODE' => 'CSC 101', 'COURSE_TITLE' => 'INTRODUCTION TO COMPUTER SCIENCE'];

    $data = PortalCourseData::from($course);

    expect($data)->toBeInstanceOf(PortalCourseData::class)
        ->and($data->onlineId)->toBe($course['ID'])
        ->and($data->code)->toBe($course['COURSE_CODE'])
        ->and($data->title)->toBe($course['COURSE_TITLE']);
});
