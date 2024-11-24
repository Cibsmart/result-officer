<?php

declare(strict_types=1);

use App\Actions\Vetting\VerifyElectiveCourses;
use App\Enums\CourseType;
use App\Enums\VettingStatus;
use App\Models\VettingReport;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Tests\Factories\CourseFactory;
use Tests\Factories\ProgramCurriculumCourseFactory;
use Tests\Factories\ProgramCurriculumElectiveCourseFactory;
use Tests\Factories\ProgramCurriculumElectiveGroupFactory;
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

covers(VerifyElectiveCourses::class);

it('skips elective course check for students without courses or matches', function (): void {
    $student = StudentFactory::new()->has(VettingEventFactory::new())->createOne();

    $action = new VerifyElectiveCourses();
    $status = $action->execute($student);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::UNCHECKED)
        ->and($action->getReport())->toBe("Elective Courses Not Checked for {$student->registration_number} \n");

    assertDatabaseCount(VettingReport::class, 1);
});

it('passes elective course check for students with required elective credits and count', function (): void {
    $semester = SemesterFactory::new(['name' => 'FIRST'])->createOne();
    $courses = CourseFactory::new()->count(3)->create();

    $programCurriculum = ProgramCurriculumFactory::new()->has(
        ProgramCurriculumLevelFactory::new()
            ->has(ProgramCurriculumSemesterFactory::new()->state([
                'minimum_elective_count' => 1, 'minimum_elective_units' => 2, 'semester_id' => $semester->id,
            ])
                ->has(ProgramCurriculumCourseFactory::new()->count(3)->state(new Sequence(
                    ['course_id' => $courses[0]->id, 'course_type' => CourseType::CORE],
                    ['course_id' => $courses[1]->id, 'course_type' => CourseType::ELECTIVE, 'credit_unit' => 2],
                    ['course_id' => $courses[2]->id, 'course_type' => CourseType::ELECTIVE, 'credit_unit' => 2],
                ))),
            ),
    )->createOne();

    $programCurriculumCourse = $programCurriculum->programCurriculumCourses->last();

    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()
            ->has(SemesterEnrollmentFactory::new()->state(['semester_id' => $semester->id])
                ->has(RegistrationFactory::new()->state([
                    'course_id' => $courses[2]->id, 'credit_unit' => 2,
                    'program_curriculum_course_id' => $programCurriculumCourse->id,
                ])),
            ),
        )
        ->has(VettingEventFactory::new())
        ->createOne([
            'entry_mode' => $programCurriculum->entry_mode, 'entry_session_id' => $programCurriculum->entry_session_id,
            'program_id' => $programCurriculum->program->id,
        ]);

    $action = new VerifyElectiveCourses();
    $status = $action->execute($student);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::PASSED)
        ->and($action->getReport())->toBe('');

    assertDatabaseEmpty(VettingReport::class);
});

it('fails elective course check for students lacking required elective credit units', function (): void {
    $semester = SemesterFactory::new(['name' => 'FIRST'])->createOne();
    $courses = CourseFactory::new()->count(2)->create();

    $programCurriculum = ProgramCurriculumFactory::new()->has(
        ProgramCurriculumLevelFactory::new()
            ->has(ProgramCurriculumSemesterFactory::new()->state([
                'minimum_elective_units' => 2, 'semester_id' => $semester->id,
            ])
                ->has(ProgramCurriculumCourseFactory::new()->count(2)->state(new Sequence(
                    ['course_id' => $courses[0]->id, 'course_type' => CourseType::ELECTIVE, 'credit_unit' => 1],
                    ['course_id' => $courses[1]->id, 'course_type' => CourseType::ELECTIVE, 'credit_unit' => 1],
                ))),
            ),
    )->createOne();

    $programCurriculumCourse = $programCurriculum->programCurriculumCourses->first();

    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()
            ->has(SemesterEnrollmentFactory::new()->state(['semester_id' => $semester->id])
                ->has(RegistrationFactory::new()->state([
                    'course_id' => $courses[0]->id, 'credit_unit' => 1,
                    'program_curriculum_course_id' => $programCurriculumCourse->id,
                ])),
            ),
        )
        ->has(VettingEventFactory::new())
        ->createOne([
            'entry_mode' => $programCurriculum->entry_mode, 'entry_session_id' => $programCurriculum->entry_session_id,
            'program_id' => $programCurriculum->program->id,
        ]);

    $action = new VerifyElectiveCourses();
    $status = $action->execute($student);

    $programCurriculumSemester = $programCurriculum->programCurriculumSemesters()->first();
    $level = $programCurriculumSemester->programCurriculumLevel->level;
    $semester = $programCurriculumSemester->semester;

    $message = "Insufficient elective course unit for {$level->name} Level {$semester->name} Semester\n";

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::FAILED)
        ->and($action->getReport())->toBe($message);

    assertDatabaseCount(VettingReport::class, 1);
});

it('fails elective course check for students lacking required elective count', function (): void {
    $semester = SemesterFactory::new(['name' => 'FIRST'])->createOne();
    $courses = CourseFactory::new()->count(2)->create();

    $programCurriculum = ProgramCurriculumFactory::new()->has(
        ProgramCurriculumLevelFactory::new()
            ->has(ProgramCurriculumSemesterFactory::new()->state([
                'minimum_elective_count' => 2, 'semester_id' => $semester->id,
            ])
                ->has(ProgramCurriculumCourseFactory::new()->count(2)->state(new Sequence(
                    ['course_id' => $courses[0]->id, 'course_type' => CourseType::ELECTIVE, 'credit_unit' => 2],
                    ['course_id' => $courses[1]->id, 'course_type' => CourseType::ELECTIVE, 'credit_unit' => 2],
                ))),
            ),
    )->createOne();

    $programCurriculumCourse = $programCurriculum->programCurriculumCourses->first();

    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()
            ->has(SemesterEnrollmentFactory::new()->state(['semester_id' => $semester->id])
                ->has(RegistrationFactory::new()->state([
                    'course_id' => $courses[1]->id, 'credit_unit' => 2,
                    'program_curriculum_course_id' => $programCurriculumCourse->id,
                ])),
            ),
        )
        ->has(VettingEventFactory::new())
        ->createOne([
            'entry_mode' => $programCurriculum->entry_mode, 'entry_session_id' => $programCurriculum->entry_session_id,
            'program_id' => $programCurriculum->program->id,
        ]);

    $action = new VerifyElectiveCourses();
    $status = $action->execute($student);

    $programCurriculumSemester = $programCurriculum->programCurriculumSemesters()->first();
    $level = $programCurriculumSemester->programCurriculumLevel->level;
    $semester = $programCurriculumSemester->semester;

    $message = "Insufficient elective course count for {$level->name} Level {$semester->name} Semester\n";

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::FAILED)
        ->and($action->getReport())->toBe($message);

    assertDatabaseCount(VettingReport::class, 1);
});

it('fails elective course check for students missing complete elective groups', function (): void {
    $semester = SemesterFactory::new(['name' => 'FIRST'])->createOne();
    $courses = CourseFactory::new()->count(3)->create();

    $programCurriculum = ProgramCurriculumFactory::new()->has(
        ProgramCurriculumLevelFactory::new()
            ->has(ProgramCurriculumSemesterFactory::new()->state([
                'minimum_elective_count' => 2,
                'minimum_elective_units' => 4,
                'semester_id' => $semester->id,
            ])
                ->has(ProgramCurriculumCourseFactory::new()->count(3)
                    ->sequence(fn (Sequence $sequence) => ['course_id' => $courses[$sequence->index]->id])
                    ->state(['course_type' => CourseType::ELECTIVE, 'credit_unit' => 2])),
            ),
    )->createOne();

    $programSemester = $programCurriculum->programCurriculumSemesters->firstOrFail();
    $programCourses = $programSemester->programCurriculumCourses;

    $electiveGroup = ProgramCurriculumElectiveGroupFactory::new()->for($programSemester)
        ->has(ProgramCurriculumElectiveCourseFactory::new()->count(2)
            ->sequence(fn (Sequence $sequence) => [
                'program_curriculum_course_id' => $programCourses[$sequence->index]->id,
            ]))
        ->createOne();

    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()
            ->has(SemesterEnrollmentFactory::new()->state(['semester_id' => $semester->id])
                ->has(RegistrationFactory::new()->count(2)->sequence(
                    [
                        'course_id' => $courses->first->id, 'credit_unit' => 2,
                        'program_curriculum_course_id' => $programCourses->first()->id,
                    ],
                    [
                        'course_id' => $courses->last()->id, 'credit_unit' => 2,
                        'program_curriculum_course_id' => $programCourses->last()->id,
                    ],
                )),
            ),
        )
        ->has(VettingEventFactory::new())
        ->createOne([
            'entry_mode' => $programCurriculum->entry_mode, 'entry_session_id' => $programCurriculum->entry_session_id,
            'program_id' => $programCurriculum->program->id,
        ]);

    $action = new VerifyElectiveCourses();
    $status = $action->execute($student);

    $level = $programSemester->programCurriculumLevel->level;
    $semester = $programSemester->semester;
    $message = "Did not take all courses in elective group ({$electiveGroup->name}) for ";
    $message .= "{$level->name} Level {$semester->name} Semester\n";

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::FAILED)
        ->and($action->getReport())->toBe($message);

    assertDatabaseCount(VettingReport::class, 1);
});

it('passes elective course check for students with complete elective groups', function (): void {
    $semester = SemesterFactory::new(['name' => 'FIRST'])->createOne();
    $courses = CourseFactory::new()->count(3)->create();

    $programCurriculum = ProgramCurriculumFactory::new()->has(
        ProgramCurriculumLevelFactory::new()
            ->has(ProgramCurriculumSemesterFactory::new()->state([
                'minimum_elective_count' => 2, 'minimum_elective_units' => 4, 'semester_id' => $semester->id,
            ])
                ->has(ProgramCurriculumCourseFactory::new()->count(3)
                    ->sequence(fn (Sequence $sequence) => ['course_id' => $courses[$sequence->index]->id])
                    ->state(['course_type' => CourseType::ELECTIVE, 'credit_unit' => 2])),
            ),
    )->createOne();

    $programSemester = $programCurriculum->programCurriculumSemesters->firstOrFail();
    $programCourses = $programSemester->programCurriculumCourses;

    ProgramCurriculumElectiveGroupFactory::new()->for($programSemester)
        ->has(ProgramCurriculumElectiveCourseFactory::new()->count(2)
            ->sequence(fn (Sequence $sequence) => [
                'program_curriculum_course_id' => $programCourses[$sequence->index]->id,
            ]))
        ->createOne();

    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()
            ->has(SemesterEnrollmentFactory::new()->state(['semester_id' => $semester->id])
                ->has(RegistrationFactory::new()->count(2)->sequence(
                    [
                        'course_id' => $courses->first()->id, 'credit_unit' => 2,
                        'program_curriculum_course_id' => $programCourses->first()->id,
                    ],
                    [
                        'course_id' => $courses[1]->id, 'credit_unit' => 2,
                        'program_curriculum_course_id' => $programCourses[1]->id,
                    ],
                )),
            ),
        )
        ->has(VettingEventFactory::new())
        ->createOne([
            'entry_mode' => $programCurriculum->entry_mode, 'entry_session_id' => $programCurriculum->entry_session_id,
            'program_id' => $programCurriculum->program->id,
        ]);

    $action = new VerifyElectiveCourses();
    $status = $action->execute($student);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::PASSED)
        ->and($action->getReport())->toBe('');

    assertDatabaseEmpty(VettingReport::class);
});
