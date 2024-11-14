<?php

declare(strict_types=1);

use App\Actions\Vetting\VerifySemesterCreditLimits;
use App\Enums\VettingStatus;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Tests\Factories\RegistrationFactory;
use Tests\Factories\SemesterEnrollmentFactory;
use Tests\Factories\SemesterFactory;
use Tests\Factories\SessionEnrollmentFactory;
use Tests\Factories\StudentFactory;
use Tests\Factories\VettingEventFactory;
use Tests\Factories\VettingStepFactory;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseEmpty;

covers(VerifySemesterCreditLimits::class);

it('checks and reports passed for student result semester total unit within limit', function (): void {
    $firstSemester = SemesterFactory::new(['name' => 'FIRST'])->createOne();
    $secondSemester = SemesterFactory::new(['name' => 'SECOND'])->createOne();

    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()
            ->has(SemesterEnrollmentFactory::new()->count(2)->state(new Sequence(
                ['semester_id' => $firstSemester->id],
                ['semester_id' => $secondSemester->id], ))
                ->has(RegistrationFactory::new()->count(8)->state(['credit_unit' => 3])),
            ),
        )->createOne();

    $vettingEvent = VettingEventFactory::new()->createOne(['student_id' => $student->id]);
    $vettingStep = VettingStepFactory::new()->createOne(['vetting_event_id' => $vettingEvent->id]);

    $action = new VerifySemesterCreditLimits();

    $status = $action->execute($student, $vettingStep);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::PASSED)
        ->and($action->report())->toBe('');

    assertDatabaseEmpty('vetting_reports');
});

it('checks and report failed for student result semester total unit above limit', function (): void {
    $firstSemester = SemesterFactory::new(['name' => 'FIRST'])->createOne();

    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()
            ->has(SemesterEnrollmentFactory::new()->state(['semester_id' => $firstSemester->id])
                ->has(RegistrationFactory::new()->count(9)->state(['credit_unit' => 3])),
            ),
        )->createOne();

    $vettingEvent = VettingEventFactory::new()->createOne(['student_id' => $student->id]);
    $vettingStep = VettingStepFactory::new()->createOne(['vetting_event_id' => $vettingEvent->id]);

    $action = new VerifySemesterCreditLimits();

    $status = $action->execute($student, $vettingStep);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::FAILED)
        ->and($action->report())->toBe($action->report());

    assertDatabaseCount('vetting_reports', 1);
});

it('checks and report failed for student result semester total unit below limit', function (): void {
    $firstSemester = SemesterFactory::new(['name' => 'FIRST'])->createOne();

    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()
            ->has(SemesterEnrollmentFactory::new()->state(['semester_id' => $firstSemester->id])
                ->has(RegistrationFactory::new()->count(10)->state(['credit_unit' => 1])),
            ),
        )->createOne();

    $vettingEvent = VettingEventFactory::new()->createOne(['student_id' => $student->id]);
    $vettingStep = VettingStepFactory::new()->createOne(['vetting_event_id' => $vettingEvent->id]);

    $action = new VerifySemesterCreditLimits();

    $status = $action->execute($student, $vettingStep);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::FAILED)
        ->and($action->report())->toBe($action->report());

    assertDatabaseCount('vetting_reports', 1);
});

it('checks and report unchecked for student without enrollments', function (): void {
    $student = StudentFactory::new()->createOne();

    $vettingEvent = VettingEventFactory::new()->createOne(['student_id' => $student->id]);
    $vettingStep = VettingStepFactory::new()->createOne(['vetting_event_id' => $vettingEvent->id]);

    $action = new VerifySemesterCreditLimits();

    $status = $action->execute($student, $vettingStep);

    expect($status)->toBeInstanceOf(VettingStatus::class)->toBe(VettingStatus::UNCHECKED)
        ->and($action->report())->toBe($action->report());
});
