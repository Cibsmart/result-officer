<?php

declare(strict_types=1);

use App\Data\Course\CourseData;
use Tests\Factories\CourseFactory;

test('course dto is correct', function (): void {
    $course = CourseFactory::new()->createOne();

    $data = CourseData::from($course);

    expect($data)->toBeInstanceOf(CourseData::class)
        ->and($data->id)->toBe($course->id)
        ->and($data->name)->toBe($course->name);
});
