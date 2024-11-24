<?php

declare(strict_types=1);

namespace App\Services\Vetting\Steps;

use App\Actions\Vetting\ValidateResults;
use App\Contracts\VettingService;
use App\Models\VettingEvent;

final readonly class CheckResultsValidityStep implements VettingService
{
    public function __construct(private ValidateResults $action)
    {
    }

    public function check(VettingEvent $vettingEvent): void
    {
        $student = $vettingEvent->student;

        $status = $this->action->execute($student);

        $this->action->vettingStep()->updateStatus($status);
    }
}
