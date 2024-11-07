<?php

declare(strict_types=1);

namespace App\Services\Vetting\Steps;

use App\Actions\Vetting\ValidateResultsAction;
use App\Enums\VettingStatus;
use App\Enums\VettingType;
use App\Models\VettingEvent;
use App\Models\VettingStep;

use function PHPUnit\Framework\assertNotNull;

final class CheckResultsValidity
{
    public function __construct(private readonly ValidateResultsAction $action)
    {
    }

    public function check(VettingEvent $vettingEvent): void
    {
        $student = $vettingEvent->student;

        assertNotNull($student);

        $vettingStep = $this->getOrCreateVettingStep($vettingEvent);

        $this->action->execute($student, $vettingStep);

        $vettingStep->updateStatus($this->action->report());
    }

    private function getOrCreateVettingStep(VettingEvent $vettingEvent): VettingStep
    {
        return VettingStep::query()->firstOrCreate(
            ['vetting_event_id' => $vettingEvent->id, 'type' => VettingType::VALIDATE_RESULTS],
            ['status' => VettingStatus::NEW],
        );
    }
}
