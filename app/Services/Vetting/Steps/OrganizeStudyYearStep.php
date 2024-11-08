<?php

declare(strict_types=1);

namespace App\Services\Vetting\Steps;

use App\Actions\Vetting\OrganizeStudyYear;
use App\Contracts\VettingService;
use App\Enums\VettingType;
use App\Models\VettingEvent;
use App\Models\VettingStep;

use function PHPUnit\Framework\assertNotNull;

final readonly class OrganizeStudyYearStep implements VettingService
{
    public function __construct(private OrganizeStudyYear $action)
    {
    }

    public function check(VettingEvent $vettingEvent): void
    {
        $student = $vettingEvent->student;

        assertNotNull($student);

        $vettingStep = VettingStep::getOrCreateUsingVettingEvent($vettingEvent, VettingType::VALIDATE_RESULTS);

        $status = $this->action->execute($student);

        $vettingStep->updateStatusAndRemarks($status, $this->action->report());
    }
}
