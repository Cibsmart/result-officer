<?php

declare(strict_types=1);

use App\Actions\Import\Registrations\ProcessPortalRegistration;
use App\Enums\RawDataStatus;
use App\Enums\RecordSource;
use Tests\Factories\CourseFactory;
use Tests\Factories\LevelFactory;
use Tests\Factories\RawRegistrationFactory;
use Tests\Factories\RegistrationFactory;
use Tests\Factories\SemesterEnrollmentFactory;
use Tests\Factories\SemesterFactory;
use Tests\Factories\SessionEnrollmentFactory;
use Tests\Factories\SessionFactory;
use Tests\Factories\StudentFactory;

use function Pest\Laravel\assertDatabaseHas;

covers(ProcessPortalRegistration::class);

it('can process raw registration and save into the registrations table', function (): void {
    $course = CourseFactory::new()->createOne(['online_id' => 1]);

    $rawRegistration = RawRegistrationFactory::new()->createOne(['course_id' => $course->id]);

    (new ProcessPortalRegistration())->execute($rawRegistration);

    expect($rawRegistration->fresh()->status)->toBe(RawDataStatus::PROCESSED);

    assertDatabaseHas('registrations', [
        'course_id' => $course->id,
        'credit_unit' => $rawRegistration->credit_unit,
        'online_id' => $rawRegistration->online_id,
        'source' => RecordSource::PORTAL,
    ]);
});

it('does not save save duplicate registration into the registrations table', function (): void {
    $course = CourseFactory::new()->createOne(['online_id' => 1]);
    $student = StudentFactory::new()->createOne();
    $session = SessionFactory::new()->createOne();
    $level = LevelFactory::new()->createOne();
    $semester = SemesterFactory::new()->createOne();
    $sessionEnrollment = SessionEnrollmentFactory::new()->createOne([
        'level_id' => $level->id, 'session_id' => $session->id, 'student_id' => $student->id,
    ]);
    $semesterEnrollment = SemesterEnrollmentFactory::new()->createOne([
        'session_enrollment_id' => $sessionEnrollment->id, 'semester_id' => $semester->id,
    ]);

    RegistrationFactory::new()->createOne([
        'course_id' => $course->id, 'semester_enrollment_id' => $semesterEnrollment->id,
    ]);

    $rawRegistration = RawRegistrationFactory::new()->createOne(
        [
            'course_id' => $course->id,
            'level' => $level->name,
            'registration_number' => $student->registration_number,
            'semester' => $semester->name,
            'session' => $session->name,
        ],
    );

    (new ProcessPortalRegistration())->execute($rawRegistration);

    expect($rawRegistration->fresh()->status)->toBe(RawDataStatus::DUPLICATE);
});
