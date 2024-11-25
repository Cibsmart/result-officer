<?php

declare(strict_types=1);

use App\Actions\Vetting\VerifyCoreCourses;
use App\Enums\CourseType;
use App\Enums\VettingStatus;
use App\Models\VettingReport;
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

covers(VerifyCoreCourses::class);

it('reports unchecked core courses for student without courses or no matching curriculum courses', function (): void {
    $student = StudentFactory::new()->has(VettingEventFactory::new())->createOne();

    $action = new VerifyCoreCourses();

    $status = $action->execute($student);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::UNCHECKED);
});

it('reports passed core courses check for student who have taken all non-elective courses', function (): void {
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
        ->has(VettingEventFactory::new())
        ->createOne([
            'entry_mode' => $programCurriculum->entry_mode,
            'entry_session_id' => $programCurriculum->entry_session_id,
            'program_id' => $programCurriculum->program->id,
        ]);

    $action = new VerifyCoreCourses();

    $status = $action->execute($student);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::PASSED)
        ->and($action->getReport())->toBe('');

    assertDatabaseEmpty(VettingReport::class);
});

it('reports failed core courses check for student who have taken all non-elective courses', function (): void {
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

    $programCurriculumCourse = $programCurriculum->programCurriculumCourses->last();

    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()
            ->has(SemesterEnrollmentFactory::new()->state(['semester_id' => $semester->id])
                ->has(RegistrationFactory::new()->state([
                    'course_id' => $course2->id, 'program_curriculum_course_id' => $programCurriculumCourse->id,
                ])),
            ),
        )
        ->has(VettingEventFactory::new())
        ->createOne([
            'entry_mode' => $programCurriculum->entry_mode,
            'entry_session_id' => $programCurriculum->entry_session_id,
            'program_id' => $programCurriculum->program->id,
        ]);

    $action = new VerifyCoreCourses();

    $status = $action->execute($student);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::FAILED);

    assertDatabaseCount(VettingReport::class, 1);
});
