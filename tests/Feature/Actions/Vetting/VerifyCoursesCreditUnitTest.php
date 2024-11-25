<?php

declare(strict_types=1);

use App\Actions\Vetting\VerifyCoursesCreditUnit;
use App\Enums\CreditUnit;
use App\Enums\VettingStatus;
use App\Models\VettingReport;
use Tests\Factories\ProgramCurriculumCourseFactory;
use Tests\Factories\RegistrationFactory;
use Tests\Factories\SemesterEnrollmentFactory;
use Tests\Factories\SessionEnrollmentFactory;
use Tests\Factories\StudentFactory;
use Tests\Factories\VettingEventFactory;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseEmpty;

covers(VerifyCoursesCreditUnit::class);

it('passes credit unit check for student courses with matching credit unit to curriculum courses', function (): void {
    $programCurriculumCourse = ProgramCurriculumCourseFactory::new()
        ->createOne(['credit_unit' => CreditUnit::THREE->value]);

    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()
            ->has(SemesterEnrollmentFactory::new()
                ->has(RegistrationFactory::new()->state([
                    'credit_unit' => CreditUnit::THREE->value,
                    'program_curriculum_course_id' => $programCurriculumCourse->id,
                ])),
            ),
        )
        ->has(VettingEventFactory::new())
        ->createOne();

    $action = new VerifyCoursesCreditUnit();

    $status = $action->execute($student);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::PASSED);

    assertDatabaseEmpty(VettingReport::class);
});

it(
    'failed credit unit check for student courses with unmatching credit unit to curriculum courses',
    function (): void {
        $programCurriculumCourse = ProgramCurriculumCourseFactory::new()
            ->createOne(['credit_unit' => CreditUnit::TWO->value]);

        $student = StudentFactory::new()
            ->has(SessionEnrollmentFactory::new()
                ->has(SemesterEnrollmentFactory::new()
                    ->has(RegistrationFactory::new()->state([
                        'credit_unit' => CreditUnit::THREE->value,
                        'program_curriculum_course_id' => $programCurriculumCourse->id,
                    ])),
                ),
            )
            ->has(VettingEventFactory::new())
            ->createOne();

        $action = new VerifyCoursesCreditUnit();

        $status = $action->execute($student);

        expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::FAILED);

        assertDatabaseCount(VettingReport::class, 1);
    },
);

it('reports unchecked credit unit for student courses not matched to any curriculum course', function (): void {
    $student = StudentFactory::new()->has(VettingEventFactory::new())->createOne();

    $action = new VerifyCoursesCreditUnit();

    $status = $action->execute($student);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::UNCHECKED);
});
