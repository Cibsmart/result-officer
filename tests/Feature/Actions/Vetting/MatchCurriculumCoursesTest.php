<?php

declare(strict_types=1);

use App\Actions\Vetting\MatchCurriculumCourses;
use App\Enums\VettingStatus;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Tests\Factories\CourseFactory;
use Tests\Factories\ProgramCurriculumCourseFactory;
use Tests\Factories\ProgramCurriculumFactory;
use Tests\Factories\ProgramCurriculumLevelFactory;
use Tests\Factories\ProgramCurriculumSemesterFactory;
use Tests\Factories\RegistrationFactory;
use Tests\Factories\SemesterEnrollmentFactory;
use Tests\Factories\SemesterFactory;
use Tests\Factories\SessionEnrollmentFactory;
use Tests\Factories\StudentFactory;
use Tests\Factories\VettingEventFactory;

use function Pest\Laravel\assertDatabaseHas;

covers(MatchCurriculumCourses::class);

it('matches student courses with the program curriculum courses', function (): void {
    $semester = SemesterFactory::new(['name' => 'FIRST'])->createOne();
    $course = CourseFactory::new()->createOne();

    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()
            ->has(SemesterEnrollmentFactory::new()->state(['semester_id' => $semester->id])
                ->has(RegistrationFactory::new()->state(['course_id' => $course->id])),
            ),
        )->has(VettingEventFactory::new())->createOne();

    ProgramCurriculumFactory::new()->has(
        ProgramCurriculumLevelFactory::new()
            ->has(ProgramCurriculumSemesterFactory::new()->state(['semester_id' => $semester->id])
                ->has(ProgramCurriculumCourseFactory::new()->state(['course_id' => $course->id])),
            ),
    )->createOne([
        'entry_mode' => $student->entry_mode,
        'entry_session_id' => $student->entry_session_id,
        'program_id' => $student->program->id,
    ]);

    $action = new MatchCurriculumCourses();
    $status = $action->execute($student);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::PASSED)
        ->and($action->report())->toBe('');
});

it('reports unchecked for program without curriculum', function (): void {
    $semester = SemesterFactory::new(['name' => 'FIRST'])->createOne();
    $course = CourseFactory::new()->createOne();

    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()
            ->has(SemesterEnrollmentFactory::new()->state(['semester_id' => $semester->id])
                ->has(RegistrationFactory::new()->state(['course_id' => $course->id])),
            ),
        )
        ->has(VettingEventFactory::new())
        ->createOne();

    $action = new MatchCurriculumCourses();
    $status = $action->execute($student);

    $session = $student->entrySession;
    $entryMode = $student->entry_mode;

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::UNCHECKED)
        ->and($action->report())->toBe(
            "Curriculum not found for {$student->program->name} {$session->name} ({$entryMode->value})  \n",
        );

    assertDatabaseHas('vetting_reports', [
        'status' => VettingStatus::FAILED,
        'vettable_id' => $student->program->id,
        'vettable_type' => 'program',
    ]);
});

it('reports unmatched student courses', function (): void {
    $semester = SemesterFactory::new(['name' => 'FIRST'])->createOne();
    $course = CourseFactory::new()->createOne();
    $course2 = CourseFactory::new()->createOne();

    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()
            ->has(SemesterEnrollmentFactory::new()->state(['semester_id' => $semester->id])
                ->has(RegistrationFactory::new()->count(2)
                    ->state(new Sequence(['course_id' => $course->id], ['course_id' => $course2->id]))),
            ),
        )
        ->has(VettingEventFactory::new())
        ->createOne();

    ProgramCurriculumFactory::new()->has(
        ProgramCurriculumLevelFactory::new()
            ->has(ProgramCurriculumSemesterFactory::new()->state(['semester_id' => $semester->id])
                ->has(ProgramCurriculumCourseFactory::new()->state(['course_id' => $course->id])),
            ),
    )->createOne([
        'entry_mode' => $student->entry_mode,
        'entry_session_id' => $student->entry_session_id,
        'program_id' => $student->program->id,
    ]);

    $action = new MatchCurriculumCourses();
    $status = $action->execute($student);

    $registration = $student->registrations()->with('semesterEnrollment.sessionEnrollment.session')->get()->last();
    $session = $registration->semesterEnrollment->sessionEnrollment->session;

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::FAILED)
        ->and($action->report())->toBe(
            "{$course2->code} in {$session->name} {$semester->name} Semester does not match any course in the curriculum \n",
        );

    assertDatabaseHas('vetting_reports', [
        'status' => VettingStatus::FAILED,
        'vettable_id' => $registration->id,
        'vettable_type' => 'registration',
    ]);
});
