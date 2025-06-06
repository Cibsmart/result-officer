<?php

declare(strict_types=1);

use App\Actions\Imports\Results\ProcessPortalResult;
use App\Enums\RawDataStatus;
use App\Enums\RecordSource;
use App\Values\DateValue;
use Tests\Factories\CourseFactory;
use Tests\Factories\LevelFactory;
use Tests\Factories\RawResultFactory;
use Tests\Factories\RegistrationFactory;
use Tests\Factories\ResultFactory;
use Tests\Factories\SemesterEnrollmentFactory;
use Tests\Factories\SemesterFactory;
use Tests\Factories\SessionEnrollmentFactory;
use Tests\Factories\SessionFactory;
use Tests\Factories\StudentFactory;

use function Pest\Laravel\assertDatabaseHas;

covers(ProcessPortalResult::class);

it('can process raw result and save into the results table', function (): void {
    $registration = RegistrationFactory::new()->createOne(['online_id' => 1]);
    $rawResult = RawResultFactory::new()->createOne(['registration_id' => $registration->id]);
    SessionFactory::new()->createOne(['name' => '2018/2019']);

    (new ProcessPortalResult())->execute($rawResult);

    expect($rawResult->fresh()->status)->toBe(RawDataStatus::PROCESSED);

    assertDatabaseHas('results', [
        'grade' => $rawResult->grade,
        'registration_id' => $rawResult->online_id,
        'scores' => json_encode([
            'exam' => $rawResult->exam, 'in_course' => $rawResult->in_course, 'in_course_2' => $rawResult->in_course_2,
        ]),
        'source' => RecordSource::PORTAL,
        'total_score' => $rawResult->total,
        'upload_date' => DateValue::fromValue($rawResult->upload_date)->value,
    ]);
});

test('that the total is equal to the sum of scores', function (): void {
    SessionFactory::new()->createOne(['name' => '2018/2019']);

    $registration = RegistrationFactory::new()->createOne(['online_id' => 1]);
    $exam = 30;
    $inCourse = 5;
    $inCourse2 = 10;

    $rawResult = RawResultFactory::new()->createOne([
        'exam' => $exam,
        'in_course' => $inCourse,
        'in_course_2' => $inCourse2,
        'registration_id' => $registration->id,
    ]);

    (new ProcessPortalResult())->execute($rawResult);

    expect($registration->result->total_score)->toBe($exam + $inCourse + $inCourse2);
});

it('does not save save duplicate result into the results table', function (): void {
    $course = CourseFactory::new()->createOne(['online_id' => 1]);
    $student = StudentFactory::new()->createOne();
    $session = SessionFactory::new()->createOne();
    $level = LevelFactory::new()->createOne();
    $semester = SemesterFactory::new()->createOne();
    $sessionEnrollment = SessionEnrollmentFactory::new()->createOne([
        'level_id' => $level->id, 'session_id' => $session->id, 'student_id' => $student->id,
    ]);
    $semesterEnrollment = SemesterEnrollmentFactory::new()->createOne([
        'semester_id' => $semester->id,
        'session_enrollment_id' => $sessionEnrollment->id,
    ]);

    $registration = RegistrationFactory::new()->createOne([
        'course_id' => $course->id, 'online_id' => 1, 'semester_enrollment_id' => $semesterEnrollment->id,
    ]);

    ResultFactory::new()->createOne(['registration_id' => $registration->id]);

    $rawResult = RawResultFactory::new()->createOne(['registration_id' => $registration->id]);

    (new ProcessPortalResult())->execute($rawResult);

    expect($rawResult->fresh()->status)->toBe(RawDataStatus::DUPLICATE);
});
