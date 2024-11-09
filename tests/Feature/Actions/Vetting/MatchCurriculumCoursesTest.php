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
use Tests\Factories\VettingStepFactory;

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
        )->createOne();

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

    $vettingEvent = VettingEventFactory::new()->createOne(['student_id' => $student->id]);
    $vettingStep = VettingStepFactory::new()->createOne(['vetting_event_id' => $vettingEvent->id]);

    $action = new MatchCurriculumCourses();
    $status = $action->execute($student, $vettingStep);

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
        )->createOne();

    $vettingEvent = VettingEventFactory::new()->createOne(['student_id' => $student->id]);
    $vettingStep = VettingStepFactory::new()->createOne(['vetting_event_id' => $vettingEvent->id]);

    $action = new MatchCurriculumCourses();
    $status = $action->execute($student, $vettingStep);

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
        'vetting_step_id' => $vettingStep->id,
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
        )->createOne();

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

    $vettingEvent = VettingEventFactory::new()->createOne(['student_id' => $student->id]);
    $vettingStep = VettingStepFactory::new()->createOne(['vetting_event_id' => $vettingEvent->id]);

    $action = new MatchCurriculumCourses();
    $status = $action->execute($student, $vettingStep);

    $registration = $student->registrations->last();
    $session = $registration->semesterEnrollment->sessionEnrollment->session;

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::FAILED)
        ->and($action->report())->toBe(
            "{$course2->code} in {$session->name} {$semester->name} Semester does not match any course in the curriculum \n",
        );

    assertDatabaseHas('vetting_reports', [
        'status' => VettingStatus::FAILED,
        'vettable_id' => $registration->id,
        'vettable_type' => 'registration',
        'vetting_step_id' => $vettingStep->id,
    ]);
});
