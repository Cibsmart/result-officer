<?php

declare(strict_types=1);

use App\Actions\Imports\Courses\ProcessPortalCourse;
use App\Enums\RawDataStatus;
use Tests\Factories\CourseFactory;
use Tests\Factories\RawCourseFactory;

use function Pest\Laravel\assertDatabaseHas;

covers(ProcessPortalCourse::class);

it('can process raw course and save into the courses table', function (): void {
    $rawCourse = RawCourseFactory::new()->createOne();

    (new ProcessPortalCourse())->execute($rawCourse);

    expect($rawCourse->fresh()->status)->toBe(RawDataStatus::PROCESSED);

    assertDatabaseHas('courses', [
        'code' => $rawCourse->code,
        'online_id' => $rawCourse->online_id,
        'title' => $rawCourse->title,
    ]);
});

it('does not save save duplicate course into the courses table', function (): void {
    $course = CourseFactory::new()->createOne();
    $rawCourse = RawCourseFactory::new()->createOne(['code' => $course->code, 'title' => $course->title]);

    (new ProcessPortalCourse())->execute($rawCourse);

    expect($rawCourse->fresh()->status)->toBe(RawDataStatus::DUPLICATE);
});
