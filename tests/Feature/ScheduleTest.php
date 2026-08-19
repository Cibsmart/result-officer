<?php

declare(strict_types=1);

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;

/**
 * rp:process-queued-vetting was documented as scheduled but never registered,
 * so every batch vetting request sat in QUEUED indefinitely. Single-student
 * vetting calls the command directly, which is why it went unnoticed.
 */
it('schedules every background command the application relies on', function (string $command): void {
    $scheduled = collect(app(Schedule::class)->events())
        ->map(fn (Event $event): string => $event->command ?? '')
        ->filter();

    expect($scheduled->contains(fn (string $c): bool => str_contains($c, $command)))
        ->toBeTrue("{$command} is not registered in the schedule");
})->with([
    'rp:process-queued-import',
    'rp:upload-pending-excel-imports',
    'rp:process-raw-excel-uploads',
    'rp:process-queued-vetting',
]);
