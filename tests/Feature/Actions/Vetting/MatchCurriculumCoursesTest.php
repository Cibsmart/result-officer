<?php

declare(strict_types=1);

use App\Actions\Vetting\MatchCurriculumCourses;
use App\Enums\VettingStatus;
use App\Models\Registration;
use App\Models\VettingReport;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Tests\Factories\CourseAlternativeFactory;
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

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

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

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::PASSED);
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

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::UNCHECKED);

    assertDatabaseHas(VettingReport::class, [
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

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::FAILED);

    assertDatabaseHas(VettingReport::class, [
        'status' => VettingStatus::FAILED,
        'vettable_type' => 'registration',
    ]);
});

it('matches student all courses with the program curriculum courses across levels', function (): void {
    $semester = SemesterFactory::new(['name' => 'FIRST'])->createOne();
    $courses = CourseFactory::new()->count(2)->create();
    $levels = LevelFactory::new()->count(2)->sequence(['name' => 100], ['name' => 200])->create();
    $sessions = SessionFactory::new()->count(2)
        ->sequence(['name' => '2009/2010'], ['name' => '2010/2011'])
        ->create();

    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()->count(2)
            ->sequence(
                ['level_id' => $levels[0]->id, 'session_id' => $sessions[0]->id],
                ['level_id' => $levels[1]->id, 'session_id' => $sessions[1]->id],
            )
            ->has(SemesterEnrollmentFactory::new()->state(['semester_id' => $semester->id])
                ->has(RegistrationFactory::new()->sequence(
                    ['course_id' => $courses[0]->id],
                    ['course_id' => $courses[1]->id],
                )),
            ),
        )
        ->has(VettingEventFactory::new())
        ->state(['entry_session_id' => $sessions[0]->id, 'entry_level_id' => $levels[0]->id])
        ->createOne();

    ProgramCurriculumFactory::new()->has(
        ProgramCurriculumLevelFactory::new()->count(2)
            ->sequence(['level_id' => $levels[0]->id], ['level_id' => $levels[1]->id])
            ->has(ProgramCurriculumSemesterFactory::new()->state(['semester_id' => $semester->id])
                ->has(ProgramCurriculumCourseFactory::new()
                    ->sequence(['course_id' => $courses[0]->id], ['course_id' => $courses[1]->id])),
            ),
    )->create([
        'entry_mode' => $student->entry_mode,
        'entry_session_id' => $sessions[0]->id,
        'program_id' => $student->program->id,
    ]);

    $action = new MatchCurriculumCourses();
    $status = $action->execute($student);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::PASSED);

    assertDatabaseMissing(Registration::class, ['program_curriculum_course_id' => null]);
});

it('matches student courses with the program curriculum course alternatives', function (): void {
    $semester = SemesterFactory::new(['name' => 'FIRST'])->createOne();
    $courses = CourseFactory::new()->count(2)->create();

    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()
            ->has(SemesterEnrollmentFactory::new()->state(['semester_id' => $semester->id])
                ->has(RegistrationFactory::new()->state(['course_id' => $courses->last()->id])),
            ),
        )->has(VettingEventFactory::new())->createOne();

    ProgramCurriculumFactory::new()->has(
        ProgramCurriculumLevelFactory::new()
            ->has(ProgramCurriculumSemesterFactory::new()->state(['semester_id' => $semester->id])
                ->has(ProgramCurriculumCourseFactory::new()
                    ->state(['course_id' => $courses->first()->id])
                    ->has(CourseAlternativeFactory::new()
                        ->state([
                            'alternate_course_id' => $courses->last()->id,
                            'original_course_id' => $courses->first()->id,
                        ]))),
            ),
    )->createOne([
        'entry_mode' => $student->entry_mode,
        'entry_session_id' => $student->entry_session_id,
        'program_id' => $student->program->id,
    ]);

    $action = new MatchCurriculumCourses();
    $status = $action->execute($student);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::PASSED);
});

it('matches student courses with the general course alternatives', function (): void {
    $semester = SemesterFactory::new(['name' => 'FIRST'])->createOne();
    $courses = CourseFactory::new()->count(2)->create();

    CourseAlternativeFactory::new()->state([
        'alternate_course_id' => $courses->last()->id,
        'original_course_id' => $courses->first()->id,
    ])->createOne();

    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()
            ->has(SemesterEnrollmentFactory::new()->state(['semester_id' => $semester->id])
                ->has(RegistrationFactory::new()->state(['course_id' => $courses->last()->id])),
            ),
        )->has(VettingEventFactory::new())->createOne();

    ProgramCurriculumFactory::new()->has(
        ProgramCurriculumLevelFactory::new()
            ->has(ProgramCurriculumSemesterFactory::new()->state(['semester_id' => $semester->id])
                ->has(ProgramCurriculumCourseFactory::new()
                    ->state(['course_id' => $courses->first()->id])),
            ),
    )->createOne([
        'entry_mode' => $student->entry_mode,
        'entry_session_id' => $student->entry_session_id,
        'program_id' => $student->program->id,
    ]);

    $action = new MatchCurriculumCourses();
    $status = $action->execute($student);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::PASSED);
});
