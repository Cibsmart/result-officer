<?php

declare(strict_types=1);

use App\Actions\Imports\Courses\SavePortalCourse;
use App\Data\Download\PortalCourseData;
use App\Enums\ImportEventType;
use App\Enums\RawDataStatus;
use Tests\Factories\ImportEventFactory;
use Tests\Factories\RawCourseFactory;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

covers(SavePortalCourse::class);

it('can save portal course into raw courses table', function (): void {
    $course = ['id' => '1', 'course_code' => 'CSC 101', 'course_title' => 'Introduction to Computer Science'];
    $event = ImportEventFactory::new()->createOne(['type' => ImportEventType::COURSES]);
    $data = PortalCourseData::fromArray($course);

    (new SavePortalCourse())->execute($event, $data);

    assertDatabaseHas('raw_courses', [
        'code' => $course['course_code'],
        'import_event_id' => $event->id,
        'online_id' => $course['id'],
        'status' => RawDataStatus::PENDING->value,
        'title' => $course['course_title'],
    ]);
});

it('does not save duplicate portal course', function (): void {
    $course = ['id' => '1', 'course_code' => 'CSC 101', 'course_title' => 'Introduction to Computer Science'];
    RawCourseFactory::new()->createOne([
        'code' => $course['course_code'],
        'status' => RawDataStatus::PROCESSED,
        'title' => $course['course_title'],
    ]);
    $event = ImportEventFactory::new()->createOne(['type' => ImportEventType::COURSES]);
    $data = PortalCourseData::fromArray($course);

    (new SavePortalCourse())->execute($event, $data);

    assertDatabaseCount('raw_courses', 1);
});
