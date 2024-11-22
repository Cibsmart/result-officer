<?php

declare(strict_types=1);

use App\Actions\Vetting\ValidateResults;
use App\Enums\VettingStatus;
use App\Enums\VettingType;
use App\Services\Vetting\Steps\CheckResultsValidityStep;
use Tests\Factories\VettingEventFactory;

use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\assertDatabaseHas;

covers(CheckResultsValidityStep::class);

it('validates students results and passed status', function (): void {
    $student = createStudentWithResults(1, 2);
    $vettingEvent = VettingEventFactory::new()->createOne(['student_id' => $student->id]);

    $action = new ValidateResults();
    $step = new CheckResultsValidityStep($action);

    $step->check($vettingEvent);

    assertDatabaseHas('vetting_steps', [
        'status' => VettingStatus::PASSED,
        'type' => VettingType::VALIDATE_RESULTS,
        'vetting_event_id' => $vettingEvent->id,
    ]);

    assertDatabaseEmpty('vetting_reports');
});

it('validates students tampered results with failed status', function (): void {
    $student = createStudentWithResults(1, 2);
    $vettingEvent = VettingEventFactory::new()->createOne(['student_id' => $student->id]);

    $registration = $student->registrations()->with('result')->get()->first();
    $result = $registration->result;
    $result->total_score = 120;
    $result->save();

    $action = new ValidateResults();
    $step = new CheckResultsValidityStep($action);

    $step->check($vettingEvent);

    assertDatabaseHas('vetting_steps', [
        'message' => $action->report(),
        'status' => VettingStatus::FAILED,
        'type' => VettingType::VALIDATE_RESULTS,
        'vetting_event_id' => $vettingEvent->id,
    ]);

    $vettingStep = $vettingEvent->vettingSteps->first();

    assertDatabaseHas('vetting_reports', [
        'status' => VettingStatus::FAILED,
        'vetting_step_id' => $vettingStep->id,
    ]);
});

it('validates students without results with unchecked status', function (): void {
    $vettingEvent = VettingEventFactory::new()->createOne();

    $action = new ValidateResults();
    $step = new CheckResultsValidityStep($action);

    $step->check($vettingEvent);

    assertDatabaseHas('vetting_steps', [
        'status' => VettingStatus::UNCHECKED,
        'type' => VettingType::VALIDATE_RESULTS,
        'vetting_event_id' => $vettingEvent->id,
    ]);

    assertDatabaseEmpty('vetting_reports');
});
