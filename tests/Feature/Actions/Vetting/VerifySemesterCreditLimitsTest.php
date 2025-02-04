<?php

declare(strict_types=1);

use App\Actions\Vetting\VerifySemesterCreditLimits;
use App\Enums\VettingStatus;
use App\Models\VettingReport;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Tests\Factories\ProgramCurriculumSemesterFactory;
use Tests\Factories\RegistrationFactory;
use Tests\Factories\SemesterEnrollmentFactory;
use Tests\Factories\SemesterFactory;
use Tests\Factories\SessionEnrollmentFactory;
use Tests\Factories\StudentFactory;
use Tests\Factories\VettingEventFactory;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseEmpty;

covers(VerifySemesterCreditLimits::class);

it('checks and reports passed for student credit unit within limit', function (): void {
    $firstSemester = SemesterFactory::new(['name' => 'FIRST'])->createOne();
    $secondSemester = SemesterFactory::new(['name' => 'SECOND'])->createOne();

    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()
            ->has(SemesterEnrollmentFactory::new()->count(2)->state(new Sequence(
                ['semester_id' => $firstSemester->id],
                ['semester_id' => $secondSemester->id],))
                ->has(RegistrationFactory::new()->count(8)->state(['credit_unit' => 3])),
            ),
        )->has(VettingEventFactory::new())->createOne();

    $action = new VerifySemesterCreditLimits();

    $status = $action->execute($student);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::PASSED);

    assertDatabaseEmpty(VettingReport::class);
});

it('checks and report failed for student semester credit unit above limit', function (): void {
    $firstSemester = SemesterFactory::new(['name' => 'FIRST'])->createOne();
    $programSemester = ProgramCurriculumSemesterFactory::new()->createOne(['semester_id' => $firstSemester->id]);

    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()
            ->has(SemesterEnrollmentFactory::new()
                ->state(['semester_id' => $firstSemester->id, 'program_curriculum_semester_id' => $programSemester->id])
                ->has(RegistrationFactory::new()->count(9)->state(['credit_unit' => 3])),
            ),
        )->has(VettingEventFactory::new())->createOne();

    $action = new VerifySemesterCreditLimits();

    $status = $action->execute($student);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::FAILED);

    assertDatabaseCount(VettingReport::class, 1);
});

it('checks and report failed for student credit unit below limit', function (): void {
    $firstSemester = SemesterFactory::new(['name' => 'FIRST'])->createOne();
    $programSemester = ProgramCurriculumSemesterFactory::new()->createOne(['semester_id' => $firstSemester->id]);

    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()
            ->has(SemesterEnrollmentFactory::new()
                ->state(['semester_id' => $firstSemester->id, 'program_curriculum_semester_id' => $programSemester->id])
                ->has(RegistrationFactory::new()->count(10)->state(['credit_unit' => 1])),
            ),
        )->has(VettingEventFactory::new())->createOne();

    $action = new VerifySemesterCreditLimits();

    $status = $action->execute($student);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::FAILED);

    assertDatabaseCount(VettingReport::class, 1);
});

it('checks and report unchecked for student without enrollments', function (): void {
    $student = StudentFactory::new()->has(VettingEventFactory::new())->createOne();

    $action = new VerifySemesterCreditLimits();

    $status = $action->execute($student);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::UNCHECKED)
        ->and($action->getReport())->toBe($action->getReport());
});
