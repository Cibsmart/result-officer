<?php

declare(strict_types=1);

use App\Data\Query\StudentCoursesData;
use App\Queries\StudentCourses;
use Tests\Factories\RegistrationFactory;
use Tests\Factories\ResultFactory;
use Tests\Factories\SemesterEnrollmentFactory;
use Tests\Factories\SessionEnrollmentFactory;
use Tests\Factories\StudentFactory;

covers(StudentCoursesData::class);

it('returns valid student courses data object', function (): void {
    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()
            ->has(SemesterEnrollmentFactory::new()
                ->has(RegistrationFactory::new()
                    ->has(ResultFactory::new()))))
        ->createOne();

    $sessionEnrollment = $student->sessionEnrollments->first();
    $semesterEnrollment = $sessionEnrollment->semesterEnrollments->first();
    $registration = $semesterEnrollment->registrations->first();
    $result = $registration->result;

    $session = $sessionEnrollment->session;
    $semester = $semesterEnrollment->semester;
    $course = $registration->course;

    $courses = StudentCourses::for($student)->query()->get();

    $data = StudentCoursesData::fromModel($courses->first());

    $grade = $data->grade
        ? $data->grade->value
        : null;

    expect($data)->toBeInstanceOf(StudentCoursesData::class)
        ->and($data->studentId)->toBe($student->id)
        ->and($data->registrationId)->toBe($registration->id)
        ->and($data->sessionId)->toBe($session->id)
        ->and($data->session)->toBe($session->name)
        ->and($data->semester)->toBe($semester->name)
        ->and($data->courseId)->toBe($course->id)
        ->and($data->courseCode)->toBe($course->code)
        ->and($data->courseTitle)->toBe($course->title)
        ->and($data->creditUnit)->toBe($registration->credit_unit)
        ->and($data->courseStatus)->toBe($registration->course_status)
        ->and($data->totalScore)->toBe($result->total_score)
        ->and($grade)->toBe($result->grade)
        ->and($data->gradePoint)->toBe($result->grade_point);
});

it('returns null score, grade and gp for registration without result', function (): void {
    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()
            ->has(SemesterEnrollmentFactory::new()
                ->has(RegistrationFactory::new())))
        ->createOne();

    $courses = StudentCourses::for($student)->query()->get();

    $data = StudentCoursesData::fromModel($courses->first());

    expect($data)->toBeInstanceOf(StudentCoursesData::class)
        ->and($data->totalScore)->toBeNull()
        ->and($data->grade)->toBeNull()
        ->and($data->gradePoint)->toBeNull();
});
