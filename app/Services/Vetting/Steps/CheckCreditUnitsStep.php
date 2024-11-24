<?php

declare(strict_types=1);

namespace App\Services\Vetting\Steps;

use App\Actions\Vetting\VerifyCoursesCreditUnit;
use App\Contracts\VettingService;
use App\Models\VettingEvent;

final readonly class CheckCreditUnitsStep implements VettingService
{
    public function __construct(private VerifyCoursesCreditUnit $action)
    {
    }

    public function check(VettingEvent $vettingEvent): void
    {
        $student = $vettingEvent->student;

        $status = $this->action->execute($student);

        $this->action->vettingStep()->updateStatus($status);
    }
}
