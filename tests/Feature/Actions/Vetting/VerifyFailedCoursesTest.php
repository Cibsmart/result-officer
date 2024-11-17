<?php

declare(strict_types=1);

use App\Actions\Vetting\VerifyFailedCourses;
use App\Enums\Grade;
use App\Enums\VettingStatus;
use App\Enums\Year;
use App\Models\VettingReport;
use Tests\Factories\CourseFactory;
use Tests\Factories\LevelFactory;
use Tests\Factories\RegistrationFactory;
use Tests\Factories\ResultFactory;
use Tests\Factories\SemesterEnrollmentFactory;
use Tests\Factories\SemesterFactory;
use Tests\Factories\SessionEnrollmentFactory;
use Tests\Factories\SessionFactory;
use Tests\Factories\StudentFactory;
use Tests\Factories\VettingEventFactory;
use Tests\Factories\VettingStepFactory;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseEmpty;

covers(VerifyFailedCourses::class);

it('reports failed courses not checked for student without results', function (): void {
    $student = StudentFactory::new()->createOne();

    $vettingStep = VettingStepFactory::new()->for(
        VettingEventFactory::new()->state(['student_id' => $student->id]),
    )->createOne();

    $action = new VerifyFailedCourses();

    $status = $action->execute($student, $vettingStep);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::UNCHECKED)
        ->and($action->report())->toBe(
            "Failed courses not checked for {$student->registration_number}\n",
        );

    assertDatabaseCount(VettingReport::class, 1);
});

it('reports failed courses check failed for student who have not passed all failed course', function (): void {
    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()->state(['year' => Year::FIRST])
            ->has(SemesterEnrollmentFactory::new()
                ->has(RegistrationFactory::new()
                    ->has(ResultFactory::new()->state(
                        ['grade' => 'F'],
                    )))))
        ->recycle(SessionFactory::new()->createOne())
        ->recycle(LevelFactory::new()->createOne())
        ->createOne();

    $vettingStep = VettingStepFactory::new()->for(
        VettingEventFactory::new()->state(['student_id' => $student->id]),
    )->createOne();

    $action = new VerifyFailedCourses();

    $status = $action->execute($student, $vettingStep);

    $course = $student->registrations->first()->course;
    $session = $student->sessionEnrollments->first()->session;
    $semester = $student->semesterEnrollments->first()->semester;

    $message = "Failed {$course->code} in {$session->name} {$semester->name} Semester\n";

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::FAILED)
        ->and($action->report())->toBe($message);

    assertDatabaseCount(VettingReport::class, 1);
});

it('reports failed courses check passed for student who passed all registered course', function (): void {
    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()->state(['year' => Year::FIRST])
            ->has(SemesterEnrollmentFactory::new()
                ->has(RegistrationFactory::new()
                    ->has(ResultFactory::new()->state(
                        ['grade' => 'D'],
                    )))))
        ->recycle(SessionFactory::new()->createOne())
        ->recycle(LevelFactory::new()->createOne())
        ->createOne();

    $vettingStep = VettingStepFactory::new()->for(
        VettingEventFactory::new()->state(['student_id' => $student->id]),
    )->createOne();

    $action = new VerifyFailedCourses();

    $status = $action->execute($student, $vettingStep);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::PASSED)
        ->and($action->report())->toBe('');

    assertDatabaseEmpty(VettingReport::class);
});

it('reports failed courses check passed for student who have passed all failed courses', function (): void {
    $sessions = SessionFactory::new()->count(2)
        ->sequence(['name' => '2009/2010'], ['name' => '2010/2011'])
        ->create();
    $levels = LevelFactory::new()->count(2)->create();
    $semester = SemesterFactory::new()->createOne(['name' => 'FIRST']);
    $course = CourseFactory::new()->createOne();

    $student = StudentFactory::new()->createOne(['entry_session_id' => $sessions[0]->id]);

    SessionEnrollmentFactory::new()->for($student)
        ->has(SemesterEnrollmentFactory::new()->state(['semester_id' => $semester->id])
            ->has(RegistrationFactory::new()->state(['course_id' => $course->id])
                ->has(ResultFactory::new()->state(['grade' => Grade::F->value]))))
        ->createOne(
            ['level_id' => $levels->first()->id, 'session_id' => $sessions->first()->id, 'year' => Year::FIRST],
        );

    SessionEnrollmentFactory::new()->for($student)
        ->has(SemesterEnrollmentFactory::new()->state(['semester_id' => $semester->id])
            ->has(RegistrationFactory::new()->state(['course_id' => $course->id])
                ->has(ResultFactory::new()->state(['grade' => Grade::C->value]))))
        ->createOne(
            ['level_id' => $levels->last()->id, 'session_id' => $sessions->last()->id, 'year' => Year::SECOND],
        );

    $vettingStep = VettingStepFactory::new()->for(
        VettingEventFactory::new()->state(['student_id' => $student->id]),
    )->createOne();

    $action = new VerifyFailedCourses();

    $status = $action->execute($student, $vettingStep);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::PASSED)
        ->and($action->report())->toBe('');

    assertDatabaseEmpty(VettingReport::class);
});

it('reports failed courses check failed for student who registered and did not take a course', function (): void {
    $session = SessionFactory::new()->createOne();
    $level = LevelFactory::new()->createOne();
    $semester = SemesterFactory::new()->createOne(['name' => 'FIRST']);
    $course = CourseFactory::new()->createOne();

    $student = StudentFactory::new()->createOne(['entry_session_id' => $session->id]);

    SessionEnrollmentFactory::new()->for($student)
        ->has(SemesterEnrollmentFactory::new()->state(['semester_id' => $semester->id])
            ->has(RegistrationFactory::new()->state(['course_id' => $course->id])))
        ->createOne(
            ['level_id' => $level->id, 'session_id' => $session->id, 'year' => Year::FIRST],
        );

    $vettingStep = VettingStepFactory::new()->for(
        VettingEventFactory::new()->state(['student_id' => $student->id]),
    )->createOne();

    $action = new VerifyFailedCourses();

    $status = $action->execute($student, $vettingStep);

    $course = $student->registrations->first()->course;
    $session = $student->sessionEnrollments->first()->session;
    $semester = $student->semesterEnrollments->first()->semester;

    $message = "Failed {$course->code} in {$session->name} {$semester->name} Semester\n";

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::FAILED)
        ->and($action->report())->toBe($message);

    assertDatabaseCount(VettingReport::class, 1);
});
