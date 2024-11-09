<?php

declare(strict_types=1);

namespace App\Services\Vetting\Steps;

use App\Actions\Vetting\VerifySemesterCreditLimits;
use App\Contracts\VettingService;
use App\Enums\VettingType;
use App\Models\VettingEvent;
use App\Models\VettingStep;

final class CheckSemesterCreditLimitsStep implements VettingService
{
    public function __construct(private VerifySemesterCreditLimits $action)
    {
    }

    public function check(VettingEvent $vettingEvent): void
    {
        $student = $vettingEvent->student;

        $vettingStep = VettingStep::getOrCreateUsingVettingEvent($vettingEvent,
            VettingType::CHECK_SEMESTER_CREDIT_UNITS);

        $status = $this->action->execute($student, $vettingStep);

        $vettingStep->updateStatusAndRemarks($status, $this->action->report());
    }
}
