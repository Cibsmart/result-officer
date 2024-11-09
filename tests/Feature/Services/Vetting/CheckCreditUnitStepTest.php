<?php

declare(strict_types=1);

use App\Actions\Vetting\VerifyCreditUnitLimits;
use App\Enums\VettingStatus;
use App\Enums\VettingType;
use App\Services\Vetting\Steps\CheckCreditUnitStep;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Tests\Factories\RegistrationFactory;
use Tests\Factories\SemesterEnrollmentFactory;
use Tests\Factories\SemesterFactory;
use Tests\Factories\SessionEnrollmentFactory;
use Tests\Factories\StudentFactory;
use Tests\Factories\VettingEventFactory;

use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\assertDatabaseHas;

covers(CheckCreditUnitStep::class);

it('checks students semester credit unit within credit limit with passed status', function (): void {
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

    $action = new VerifyCreditUnitLimits();
    $step = new CheckCreditUnitStep($action);

    $step->check($vettingEvent);

    assertDatabaseHas('vetting_steps', [
        'status' => VettingStatus::PASSED,
        'type' => VettingType::CHECK_CREDIT_UNITS,
        'vetting_event_id' => $vettingEvent->id,
    ]);

    assertDatabaseEmpty('vetting_reports');
});

it('checks students semester results below minimum credit limit with failed status', function (): void {
    $firstSemester = SemesterFactory::new(['name' => 'FIRST'])->createOne();
    $secondSemester = SemesterFactory::new(['name' => 'SECOND'])->createOne();

    $student = StudentFactory::new()
        ->has(SessionEnrollmentFactory::new()
            ->has(SemesterEnrollmentFactory::new()->count(2)->state(new Sequence(
                ['semester_id' => $firstSemester->id],
                ['semester_id' => $secondSemester->id], ))
                ->has(RegistrationFactory::new()->count(9)->state(['credit_unit' => 3])),
            ),
        )->createOne();

    $vettingEvent = VettingEventFactory::new()->createOne(['student_id' => $student->id]);

    $action = new VerifyCreditUnitLimits();
    $step = new CheckCreditUnitStep($action);

    $step->check($vettingEvent);

    assertDatabaseHas('vetting_steps', [
        'message' => $action->report(),
        'status' => VettingStatus::FAILED,
        'type' => VettingType::CHECK_CREDIT_UNITS,
        'vetting_event_id' => $vettingEvent->id,
    ]);

    $vettingStep = $vettingEvent->vettingSteps->first();

    assertDatabaseHas('vetting_reports', [
        'status' => VettingStatus::FAILED,
        'vetting_step_id' => $vettingStep->id,
    ]);
});

it('checks students without results with unchecked status', function (): void {
    $vettingEvent = VettingEventFactory::new()->createOne();

    $action = new VerifyCreditUnitLimits();
    $step = new CheckCreditUnitStep($action);

    $step->check($vettingEvent);

    assertDatabaseHas('vetting_steps', [
        'status' => VettingStatus::UNCHECKED,
        'type' => VettingType::CHECK_CREDIT_UNITS,
        'vetting_event_id' => $vettingEvent->id,
    ]);

    assertDatabaseEmpty('vetting_reports');
});
