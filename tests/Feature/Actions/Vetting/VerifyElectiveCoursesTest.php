<?php

declare(strict_types=1);

use App\Actions\Vetting\VerifyElectiveCourses;
use App\Enums\CourseType;
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

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseEmpty;

it('reports elective courses not checked for student without courses or no matching  courses', function (): void {
    $student = StudentFactory::new()->createOne();

    $vettingEvent = VettingEventFactory::new()->createOne(['student_id' => $student->id]);
    $vettingStep = VettingStepFactory::new()->createOne(['vetting_event_id' => $vettingEvent->id]);

    $action = new VerifyElectiveCourses();

    $status = $action->execute($student, $vettingStep);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::UNCHECKED)
        ->and($action->report())->toBe(
            "Elective Courses Not Checked for {$student->registration_number} \n",
        );

    assertDatabaseCount('vetting_reports', 1);
});

it('reports elective courses passed for student who have taken necessary elective courses', function (): void {
    $semester = SemesterFactory::new(['name' => 'FIRST'])->createOne();
    $course = CourseFactory::new()->createOne();
    $course2 = CourseFactory::new()->createOne();
    $course3 = CourseFactory::new()->createOne();

    $programCurriculum = ProgramCurriculumFactory::new()->has(
        ProgramCurriculumLevelFactory::new()
            ->has(ProgramCurriculumSemesterFactory::new()->state([
                'minimum_elective_count' => 1,
                'minimum_elective_units' => 2,
                'semester_id' => $semester->id,
            ])
                ->has(ProgramCurriculumCourseFactory::new()->count(3)->state(new Sequence(
                    ['course_id' => $course->id, 'course_type' => CourseType::CORE],
                    ['course_id' => $course2->id, 'course_type' => CourseType::ELECTIVE, 'credit_unit' => 2],
                    ['course_id' => $course3->id, 'course_type' => CourseType::ELECTIVE, 'credit_unit' => 2],
                ))),
            ),
    )->createOne();

    $programCurriculumCourse = $programCurriculum->programCurriculumCourses->last();

    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()
            ->has(SemesterEnrollmentFactory::new()->state(['semester_id' => $semester->id])
                ->has(RegistrationFactory::new()->state([
                    'course_id' => $course3->id,
                    'credit_unit' => 2,
                    'program_curriculum_course_id' => $programCurriculumCourse->id,
                ])),
            ),
        )->createOne([
            'entry_mode' => $programCurriculum->entry_mode,
            'entry_session_id' => $programCurriculum->entry_session_id,
            'program_id' => $programCurriculum->program->id,
        ]);

    $vettingEvent = VettingEventFactory::new()->createOne(['student_id' => $student->id]);
    $vettingStep = VettingStepFactory::new()->createOne(['vetting_event_id' => $vettingEvent->id]);

    $action = new VerifyElectiveCourses();

    $status = $action->execute($student, $vettingStep);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::PASSED)
        ->and($action->report())->toBe('');

    assertDatabaseEmpty('vetting_reports');
});

it('reports elective courses failed for student who have not taken necessary elective courses', function (): void {
    $semester = SemesterFactory::new(['name' => 'FIRST'])->createOne();
    $course = CourseFactory::new()->createOne();
    $course2 = CourseFactory::new()->createOne();
    $course3 = CourseFactory::new()->createOne();

    $programCurriculum = ProgramCurriculumFactory::new()->has(
        ProgramCurriculumLevelFactory::new()
            ->has(ProgramCurriculumSemesterFactory::new()->state([
                'minimum_elective_count' => 1,
                'minimum_elective_units' => 2,
                'semester_id' => $semester->id,
            ])
                ->has(ProgramCurriculumCourseFactory::new()->count(3)->state(new Sequence(
                    ['course_id' => $course->id, 'course_type' => CourseType::CORE],
                    ['course_id' => $course2->id, 'course_type' => CourseType::ELECTIVE, 'credit_unit' => 2],
                    ['course_id' => $course3->id, 'course_type' => CourseType::ELECTIVE, 'credit_unit' => 2],
                ))),
            ),
    )->createOne();

    $programCurriculumCourse = $programCurriculum->programCurriculumCourses->first();

    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()
            ->has(SemesterEnrollmentFactory::new()->state(['semester_id' => $semester->id])
                ->has(RegistrationFactory::new()->state([
                    'course_id' => $course->id,
                    'credit_unit' => 2,
                    'program_curriculum_course_id' => $programCurriculumCourse->id,
                ])),
            ),
        )->createOne([
            'entry_mode' => $programCurriculum->entry_mode,
            'entry_session_id' => $programCurriculum->entry_session_id,
            'program_id' => $programCurriculum->program->id,
        ]);

    $vettingEvent = VettingEventFactory::new()->createOne(['student_id' => $student->id]);
    $vettingStep = VettingStepFactory::new()->createOne(['vetting_event_id' => $vettingEvent->id]);

    $action = new VerifyElectiveCourses();

    $status = $action->execute($student, $vettingStep);

    $programCurriculumSemester = $programCurriculum->programCurriculumSemesters()->first();
    $level = $programCurriculumSemester->programCurriculumLevel->level;
    $semester = $programCurriculumSemester->semester;

    $message = "Elective Course(s) for {$level->name} Level {$semester->name} Semester not complete \n";

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::FAILED)
        ->and($action->report())->toBe($message);

    assertDatabaseCount('vetting_reports', 1);
});
