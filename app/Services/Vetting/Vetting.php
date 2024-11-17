<?php

declare(strict_types=1);

namespace App\Services\Vetting;

use App\Enums\VettingEventStatus;
use App\Models\VettingEvent;

final readonly class Vetting
{
    /** @param array<string, \App\Contracts\VettingService> $vettingSteps */
    public function __construct(private array $vettingSteps)
    {
    }

    public function vet(VettingEvent $vettingEvent): void
    {
        $vettingEvent->updateStatus(VettingEventStatus::VETTING);

        foreach ($this->vettingSteps as $type => $step) {
            $step->check($vettingEvent);
        }

        $vettingEvent->updateVettingStatus();
    }
}
