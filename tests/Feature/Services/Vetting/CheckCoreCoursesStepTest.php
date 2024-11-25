<?php

declare(strict_types=1);

use App\Actions\Vetting\VerifyCoreCourses;
use App\Enums\CourseType;
use App\Enums\VettingStatus;
use App\Enums\VettingType;
use App\Models\VettingReport;
use App\Models\VettingStep;
use App\Services\Vetting\Steps\CheckCoreCoursesStep;
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

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\assertDatabaseHas;

covers(CheckCoreCoursesStep::class);

it('checks if student has taken all core course and report passed status if true', function (): void {
    $semester = SemesterFactory::new(['name' => 'FIRST'])->createOne();
    $course = CourseFactory::new()->createOne();
    $course2 = CourseFactory::new()->createOne();

    $programCurriculum = ProgramCurriculumFactory::new()->has(
        ProgramCurriculumLevelFactory::new()
            ->has(ProgramCurriculumSemesterFactory::new()->state(['semester_id' => $semester->id])
                ->has(ProgramCurriculumCourseFactory::new()->count(2)->state(new Sequence(
                    ['course_id' => $course->id, 'course_type' => CourseType::CORE],
                    ['course_id' => $course2->id, 'course_type' => CourseType::ELECTIVE],
                ))),
            ),
    )->createOne();

    $programCurriculumCourse = $programCurriculum->programCurriculumCourses->first();

    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()
            ->has(SemesterEnrollmentFactory::new()->state(['semester_id' => $semester->id])
                ->has(RegistrationFactory::new()->state([
                    'course_id' => $course->id, 'program_curriculum_course_id' => $programCurriculumCourse->id,
                ])),
            ),
        )
        ->createOne([
            'entry_mode' => $programCurriculum->entry_mode,
            'entry_session_id' => $programCurriculum->entry_session_id,
            'program_id' => $programCurriculum->program->id,
        ]);

    $vettingEvent = VettingEventFactory::new()->for($student)->createOne();

    $action = new VerifyCoreCourses();
    $step = new CheckCoreCoursesStep($action);

    $step->check($vettingEvent);

    assertDatabaseHas(VettingStep::class, [
        'status' => VettingStatus::PASSED,
        'type' => VettingType::CHECK_CORE_COURSES,
        'vetting_event_id' => $vettingEvent->id,
    ]);

    assertDatabaseEmpty('vetting_reports');
});

it('checks if student has taken all core course and report failed status if false', function (): void {
    $semester = SemesterFactory::new(['name' => 'FIRST'])->createOne();
    $course = CourseFactory::new()->createOne();
    $course2 = CourseFactory::new()->createOne();

    $programCurriculum = ProgramCurriculumFactory::new()->has(
        ProgramCurriculumLevelFactory::new()
            ->has(ProgramCurriculumSemesterFactory::new()->state(['semester_id' => $semester->id])
                ->has(ProgramCurriculumCourseFactory::new()->count(2)->state(new Sequence(
                    ['course_id' => $course->id, 'course_type' => CourseType::CORE],
                    ['course_id' => $course2->id, 'course_type' => CourseType::CORE],
                ))),
            ),
    )->createOne();

    $programCurriculumCourse = $programCurriculum->programCurriculumCourses->first();

    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()
            ->has(SemesterEnrollmentFactory::new()->state(['semester_id' => $semester->id])
                ->has(RegistrationFactory::new()->state([
                    'course_id' => $course->id, 'program_curriculum_course_id' => $programCurriculumCourse->id,
                ])),
            ),
        )
        ->createOne([
            'entry_mode' => $programCurriculum->entry_mode,
            'entry_session_id' => $programCurriculum->entry_session_id,
            'program_id' => $programCurriculum->program->id,
        ]);

    $vettingEvent = VettingEventFactory::new()->for($student)->createOne();

    $action = new VerifyCoreCourses();
    $step = new CheckCoreCoursesStep($action);

    $step->check($vettingEvent);

    assertDatabaseHas('vetting_steps', [
        'status' => VettingStatus::FAILED,
        'type' => VettingType::CHECK_CORE_COURSES,
        'vetting_event_id' => $vettingEvent->id,
    ]);

    assertDatabaseCount(VettingReport::class, 1);
});

it('checks students without courses and unchecked', function (): void {
    $vettingEvent = VettingEventFactory::new()->createOne();

    $action = new VerifyCoreCourses();
    $step = new CheckCoreCoursesStep($action);

    $step->check($vettingEvent);

    assertDatabaseHas('vetting_steps', [
        'status' => VettingStatus::UNCHECKED,
        'type' => VettingType::CHECK_CORE_COURSES,
        'vetting_event_id' => $vettingEvent->id,
    ]);
});
