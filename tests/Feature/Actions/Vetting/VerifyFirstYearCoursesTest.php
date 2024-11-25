<?php

declare(strict_types=1);

use App\Actions\Vetting\VerifyFirstYearCourses;
use App\Enums\CourseType;
use App\Enums\LevelEnum;
use App\Enums\VettingStatus;
use App\Models\VettingReport;
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
use Tests\Factories\StudentFactory;
use Tests\Factories\VettingEventFactory;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseEmpty;

covers(VerifyFirstYearCourses::class);

it('reports first year courses not checked for student without courses or no matching courses ', function (): void {
    $student = StudentFactory::new()->has(VettingEventFactory::new())->createOne();

    $action = new VerifyFirstYearCourses();

    $status = $action->execute($student);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::UNCHECKED);

    assertDatabaseCount(VettingReport::class, 1);
});

it('reports first year courses passed for student who took all required first year courses', function (): void {
    $semester = SemesterFactory::new()->createOne(['name' => 'FIRST']);
    $level = LevelFactory::new()->createOne(['name' => LevelEnum::LEVEL_100]);
    $course = CourseFactory::new()->createOne();
    $course2 = CourseFactory::new()->createOne();

    $programCurriculum = ProgramCurriculumFactory::new()->has(
        ProgramCurriculumLevelFactory::new()->state(['level_id' => $level->id])
            ->has(ProgramCurriculumSemesterFactory::new()->state(['semester_id' => $semester->id])
                ->has(ProgramCurriculumCourseFactory::new()->count(2)->sequence(
                    ['course_id' => $course->id, 'course_type' => CourseType::CORE],
                    ['course_id' => $course2->id, 'course_type' => CourseType::ELECTIVE],
                )),
            ),
    )->createOne();

    $programCurriculumCourse = $programCurriculum->programCurriculumCourses->first();
    $sessionId = $programCurriculum->entry_session_id;

    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()->state(['session_id' => $sessionId, 'level_id' => $level->id])
            ->has(SemesterEnrollmentFactory::new()->state(['semester_id' => $semester->id])
                ->has(RegistrationFactory::new()->state([
                    'course_id' => $course->id, 'program_curriculum_course_id' => $programCurriculumCourse->id,
                ])),
            ),
        )
        ->has(VettingEventFactory::new())
        ->createOne([
            'entry_level_id' => $level->id,
            'entry_mode' => $programCurriculum->entry_mode,
            'entry_session_id' => $sessionId,
            'program_id' => $programCurriculum->program->id,
        ]);

    $action = new VerifyFirstYearCourses();

    $status = $action->execute($student);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::PASSED);

    assertDatabaseEmpty(VettingReport::class);
});

it('reports first year courses failed for student who did not taken all required first year courses',
    function (): void {
        $semester = SemesterFactory::new()->createOne(['name' => 'FIRST']);
        $level = LevelFactory::new()->createOne(['name' => LevelEnum::LEVEL_100]);
        $course = CourseFactory::new()->createOne();
        $course2 = CourseFactory::new()->createOne();

        $programCurriculum = ProgramCurriculumFactory::new()->has(
            ProgramCurriculumLevelFactory::new()->state(['level_id' => $level->id])
                ->has(ProgramCurriculumSemesterFactory::new()->state(['semester_id' => $semester->id])
                    ->has(ProgramCurriculumCourseFactory::new()->count(2)->sequence(
                        ['course_id' => $course->id, 'course_type' => CourseType::CORE],
                        ['course_id' => $course2->id, 'course_type' => CourseType::ELECTIVE],
                    )),
                ),
        )->createOne();

        $programCurriculumCourse = $programCurriculum->programCurriculumCourses->last();
        $sessionId = $programCurriculum->entry_session_id;

        $student = StudentFactory::new()
            ->has(SessionEnrollmentFactory::new()->state(['session_id' => $sessionId, 'level_id' => $level->id])
                ->has(SemesterEnrollmentFactory::new()->state(['semester_id' => $semester->id])
                    ->has(RegistrationFactory::new()->state([
                        'course_id' => $course->id, 'program_curriculum_course_id' => $programCurriculumCourse->id,
                    ])),
                ),
            )
            ->has(VettingEventFactory::new())
            ->createOne([
                'entry_level_id' => $level->id,
                'entry_mode' => $programCurriculum->entry_mode,
                'entry_session_id' => $sessionId,
                'program_id' => $programCurriculum->program->id,
            ]);

        $action = new VerifyFirstYearCourses();

        $status = $action->execute($student);

        expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::FAILED);

        assertDatabaseCount(VettingReport::class, 1);
    });
