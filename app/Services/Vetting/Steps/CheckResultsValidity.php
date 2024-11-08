<?php

declare(strict_types=1);

namespace App\Services\Vetting\Steps;

use App\Actions\Vetting\ValidateResults;
use App\Contracts\VettingService;
use App\Enums\VettingStatus;
use App\Enums\VettingType;
use App\Models\VettingEvent;
use App\Models\VettingStep;

use function PHPUnit\Framework\assertNotNull;

final readonly class CheckResultsValidity implements VettingService
{
    public function __construct(private ValidateResults $action)
    {
    }

    public function check(VettingEvent $vettingEvent): void
    {
        $student = $vettingEvent->student;

        assertNotNull($student);

        $vettingStep = VettingStep::getOrCreateUsingVettingEvent(
            vettingEvent: $vettingEvent,
            vettingType: VettingType::VALIDATE_RESULTS,
            vettingStatus: VettingStatus::NEW,
        );

        $status = $this->action->execute($student, $vettingStep);

        $vettingStep->updateStatusAndRemarks($status, $this->action->remarks());
    }
}
