<?php

declare(strict_types=1);

namespace App\Console\Commands\Courses;

use App\Actions\Courses\ProcessImportedRawCourses;
use App\Enums\ImportEventStatus;
use App\Models\ImportEvent;
use Illuminate\Console\Command;

final class ProcessImportedCourses extends Command
{
    protected $signature = 'courses:process {eventId}';

    protected $description = 'Process all Pending Raw Data associated with the event';

    public function handle(ProcessImportedRawCourses $processRawCoursesAction): void
    {
        $event = ImportEvent::findOrFail($this->argument('eventId'));

        if (in_array($event->status, ImportEventStatus::unprocessableStates(), true)) {
            return;
        }

        $processRawCoursesAction->execute($event);
    }
}
