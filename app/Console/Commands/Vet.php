<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\VettingEvent;
use App\Services\Vetting\Vetting;
use Illuminate\Console\Command;

final class Vet extends Command
{
    protected $signature = 'rp:vet {vettingEventId}';

    protected $description = "Initiate Vetting of Student's Results";

    public function handle(Vetting $vetting): void
    {
        $vettingEvent = VettingEvent::query()->findOrFail($this->argument('vettingEventId'));

        $vetting->vet($vettingEvent);
    }
}
