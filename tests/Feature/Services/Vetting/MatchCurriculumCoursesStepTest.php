<?php

declare(strict_types=1);

use App\Actions\Vetting\MatchCurriculumCourses;
use App\Enums\VettingStatus;
use App\Enums\VettingType;
use App\Services\Vetting\Steps\MatchCurriculumCoursesStep;
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

use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\assertDatabaseHas;

covers(MatchCurriculumCoursesStep::class);

it('matches matching students courses and passes check', function (): void {
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

    $action = new MatchCurriculumCourses();
    $step = new MatchCurriculumCoursesStep($action);

    $step->check($vettingEvent);

    assertDatabaseHas('vetting_steps', [
        'status' => VettingStatus::PASSED,
        'type' => VettingType::MATCH_COURSES,
        'vetting_event_id' => $vettingEvent->id,
    ]);

    assertDatabaseEmpty('vetting_reports');
});

it('matches non-matching students courses and fails check', function (): void {
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

    $action = new MatchCurriculumCourses();
    $step = new MatchCurriculumCoursesStep($action);

    $step->check($vettingEvent);

    assertDatabaseHas('vetting_steps', [
        'status' => VettingStatus::FAILED,
        'type' => VettingType::MATCH_COURSES,
        'vetting_event_id' => $vettingEvent->id,
    ]);

    $vettingStep = $vettingEvent->vettingSteps->first();

    assertDatabaseHas('vetting_reports', [
        'status' => VettingStatus::FAILED,
        'vetting_step_id' => $vettingStep->id,
    ]);
});

it('checks and does not match courses for program without curriculum', function (): void {
    $semester = SemesterFactory::new(['name' => 'FIRST'])->createOne();
    $course = CourseFactory::new()->createOne();

    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()
            ->has(SemesterEnrollmentFactory::new()->state(['semester_id' => $semester->id])
                ->has(RegistrationFactory::new()->state(['course_id' => $course->id])),
            ),
        )->createOne();

    $vettingEvent = VettingEventFactory::new()->createOne(['student_id' => $student->id]);

    $action = new MatchCurriculumCourses();
    $step = new MatchCurriculumCoursesStep($action);

    $step->check($vettingEvent);

    assertDatabaseHas('vetting_steps', [
        'status' => VettingStatus::UNCHECKED,
        'type' => VettingType::MATCH_COURSES,
        'vetting_event_id' => $vettingEvent->id,
    ]);

    $vettingStep = $vettingEvent->vettingSteps->first();

    assertDatabaseHas('vetting_reports', [
        'status' => VettingStatus::FAILED,
        'vetting_step_id' => $vettingStep->id,
    ]);
});
