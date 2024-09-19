<?php

declare(strict_types=1);

namespace App\Console\Commands\Departments;

use App\Actions\Departments\ProcessImportedRawDepartments;
use App\Enums\ImportEventStatus;
use App\Models\ImportEvent;
use Illuminate\Console\Command;

final class ProcessImportedDepartments extends Command
{
    protected $signature = 'departments:process {eventId}';

    protected $description = 'Process all Pending Raw Data associated with the event';

    public function handle(ProcessImportedRawDepartments $processRawDepartmentsAction): void
    {
        $event = ImportEvent::findOrFail($this->argument('eventId'));

        if (in_array($event->status, ImportEventStatus::unprocessableStates(), true)) {
            return;
        }

        $processRawDepartmentsAction->execute($event);
    }
}
