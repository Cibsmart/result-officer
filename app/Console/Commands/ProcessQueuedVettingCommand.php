<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\VettingEventStatus;
use App\Models\VettingEventGroup;
use App\Services\Vetting\Vetting;
use Illuminate\Console\Command;

final class ProcessQueuedVettingCommand extends Command
{
    protected $signature = 'rp:process-queued-vetting';

    protected $description = 'Select and process queued group vetting events one at a time';

    public function handle(Vetting $vettingService): int
    {
        $event = VettingEventGroup::query()
            ->with('vettingEvents.student.vettingEvent')
            ->where('status', VettingEventStatus::QUEUED)
            ->oldest()
            ->first();

        if ($event === null) {
            return Command::SUCCESS;
        }

        $event->updateStatus(VettingEventStatus::VETTING);

        $vettings = $event->vettingEvents;

        foreach ($vettings as $vetting) {
            $vettingService->vet($vetting);
        }

        $event->updateStatus(VettingEventStatus::COMPLETED);

        return Command::SUCCESS;
    }
}
