<?php

declare(strict_types=1);

use App\Actions\Vetting\MatchCurriculumSemesters;
use App\Enums\VettingStatus;
use App\Models\Program;
use App\Models\SemesterEnrollment;
use App\Models\VettingReport;
use Tests\Factories\CourseFactory;
use Tests\Factories\LevelFactory;
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

covers(MatchCurriculumSemesters::class);

it('matches student semesters with the program curriculum semesters', function (): void {
    $semester = SemesterFactory::new(['name' => 'FIRST'])->createOne();
    $level = LevelFactory::new(['name' => 100])->createOne();

    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new(['level_id' => $level->id])
            ->has(SemesterEnrollmentFactory::new()->state(['semester_id' => $semester->id])),
        )->has(VettingEventFactory::new())
        ->createOne(['entry_level_id' => $level->id]);

    ProgramCurriculumFactory::new()->has(
        ProgramCurriculumLevelFactory::new()->state(['level_id' => $level->id])
            ->has(ProgramCurriculumSemesterFactory::new()->state(['semester_id' => $semester->id])),
    )->createOne([
        'entry_mode' => $student->entry_mode,
        'entry_session_id' => $student->entry_session_id,
        'program_id' => $student->program->id,
    ]);

    $action = new MatchCurriculumSemesters();
    $status = $action->execute($student);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::PASSED);

    assertDatabaseEmpty(VettingReport::class);
});

it('reports fail for student semesters not matching any program curriculum semesters', function (): void {
    $semester = SemesterFactory::new(['name' => 'FIRST'])->createOne();
    $semester2 = SemesterFactory::new(['name' => 'SECOND'])->createOne();

    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()
            ->has(SemesterEnrollmentFactory::new()->state(['semester_id' => $semester2->id])),
        )->has(VettingEventFactory::new())->createOne();

    ProgramCurriculumFactory::new()->has(
        ProgramCurriculumLevelFactory::new()
            ->has(ProgramCurriculumSemesterFactory::new()->state(['semester_id' => $semester->id])),
    )->createOne([
        'entry_mode' => $student->entry_mode,
        'entry_session_id' => $student->entry_session_id,
        'program_id' => $student->program->id,
    ]);

    $action = new MatchCurriculumSemesters();
    $status = $action->execute($student);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::FAILED);

    assertDatabaseHas(VettingReport::class, [
        'status' => VettingStatus::FAILED,
        'vettable_id' => SemesterEnrollment::first()->id,
        'vettable_type' => new SemesterEnrollment()->getMorphClass(),
    ]);
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

    $action = new MatchCurriculumSemesters();
    $status = $action->execute($student);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::UNCHECKED);

    assertDatabaseHas(VettingReport::class, [
        'status' => VettingStatus::FAILED,
        'vettable_type' => new Program()->getMorphClass(),
    ]);
});
