<?php

declare(strict_types=1);

namespace App\Services\Vetting\Steps;

use App\Actions\Vetting\ValidateResults;
use App\Contracts\VettingService;
use App\Enums\VettingType;
use App\Models\VettingEvent;
use App\Models\VettingStep;

final readonly class CheckResultsValidityStep implements VettingService
{
    public function __construct(private ValidateResults $action)
    {
    }

    public function check(VettingEvent $vettingEvent): void
    {
        $student = $vettingEvent->student;

        $vettingStep = VettingStep::getOrCreateUsingVettingEvent($vettingEvent, VettingType::VALIDATE_RESULTS);

        $status = $this->action->execute($student, $vettingStep);

        $vettingStep->updateStatusAndRemarks($status, $this->action->report());
    }
}
