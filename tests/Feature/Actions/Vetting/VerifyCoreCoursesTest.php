<?php

declare(strict_types=1);

use App\Actions\Vetting\VerifyCoreCourses;
use App\Enums\CourseType;
use App\Enums\VettingStatus;
use App\Models\ProgramCurriculumCourse;
use App\Models\VettingReport;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Tests\Factories\CourseFactory;
use Tests\Factories\LevelFactory;
use Tests\Factories\ProgramCurriculumCourseFactory;
use Tests\Factories\ProgramCurriculumFactory;
use Tests\Factories\ProgramCurriculumLevelFactory;
use Tests\Factories\ProgramCurriculumSemesterFactory;
use Tests\Factories\RegistrationFactory;
use Tests\Factories\SemesterEnrollmentFactory;
use Tests\Factories\SemesterFactory;
use Tests\Factories\SessionEnrollmentFactory;
use Tests\Factories\SessionFactory;
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

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::PASSED);

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

it('passes core courses check for student who have taken all non-elective courses across sessions', function (): void {
    $semester = SemesterFactory::new(['name' => 'FIRST'])->createOne();
    $courses = CourseFactory::new()->count(4)->create();
    $levels = LevelFactory::new()->count(2)->sequence(['name' => 100], ['name' => 200])->create();
    $sessions = SessionFactory::new()->count(2)
        ->sequence(['name' => '2009/2010'], ['name' => '2010/2011'])
        ->create();

    $programCurriculum = ProgramCurriculumFactory::new()->has(
        ProgramCurriculumLevelFactory::new()->count(2)
            ->sequence(['level_id' => $levels[0]->id], ['level_id' => $levels[1]->id])
            ->has(ProgramCurriculumSemesterFactory::new()->state(['semester_id' => $semester->id])
                ->has(ProgramCurriculumCourseFactory::new()->count(2)->state(new Sequence(
                    ['course_id' => $courses[0]->id, 'course_type' => CourseType::CORE],
                    ['course_id' => $courses[1]->id, 'course_type' => CourseType::ELECTIVE],
                    ['course_id' => $courses[2]->id, 'course_type' => CourseType::CORE],
                    ['course_id' => $courses[3]->id, 'course_type' => CourseType::ELECTIVE],
                ))),
            ),
    )->createOne(['entry_session_id' => $sessions[0]->id]);

    $programCourses = ProgramCurriculumCourse::all();

    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()->count(2)->sequence(
            ['level_id' => $levels[0]->id, 'session_id' => $sessions[0]->id],
            ['level_id' => $levels[1]->id, 'session_id' => $sessions[1]->id],
        )
            ->has(SemesterEnrollmentFactory::new()->state(['semester_id' => $semester->id])
                ->has(RegistrationFactory::new()->count(2)->sequence(
                    ['course_id' => $courses[0]->id, 'program_curriculum_course_id' => $programCourses[0]->id],
                    ['course_id' => $courses[1]->id, 'program_curriculum_course_id' => $programCourses[1]->id],
                    ['course_id' => $courses[2]->id, 'program_curriculum_course_id' => $programCourses[2]->id],
                    ['course_id' => $courses[3]->id, 'program_curriculum_course_id' => $programCourses[3]->id],
                )),
            ),
        )
        ->has(VettingEventFactory::new())
        ->createOne([
            'entry_level_id' => $levels[0]->id,
            'entry_mode' => $programCurriculum->entry_mode,
            'entry_session_id' => $sessions[0]->id,
            'program_id' => $programCurriculum->program->id,
        ]);

    $action = new VerifyCoreCourses();

    $status = $action->execute($student);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::PASSED);

    assertDatabaseEmpty(VettingReport::class);
});
