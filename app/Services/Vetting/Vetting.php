<?php

declare(strict_types=1);

namespace App\Services\Vetting;

use App\Enums\VettingEventStatus;
use App\Models\VettingEvent;

final readonly class Vetting
{
    /** @param array<int, \App\Contracts\VettingService> $vettingSteps */
    public function __construct(private array $vettingSteps)
    {
    }

    public function vet(VettingEvent $vettingEvent): void
    {
        $vettingEvent->updateStatus(VettingEventStatus::VETTING);

        foreach ($this->vettingSteps as $step) {
            $step->check($vettingEvent);
        }

        $vettingEvent->updateStatus(VettingEventStatus::COMPLETED);
    }
}
