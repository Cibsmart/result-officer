<?php

declare(strict_types=1);

namespace App\Services\Vetting\Steps;

use App\Actions\Vetting\VerifySemesterCreditLimits;
use App\Contracts\VettingService;
use App\Models\VettingEvent;

final class CheckSemesterCreditLimitsStep implements VettingService
{
    public function __construct(private VerifySemesterCreditLimits $action)
    {
    }

    public function check(VettingEvent $vettingEvent): void
    {
        $student = $vettingEvent->student;

        $status = $this->action->execute($student);

        $this->action->vettingStep()->updateStatus($status);
    }
}
