<?php

declare(strict_types=1);

use App\Console\Commands\ProcessQueuedImportEvent;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command(ProcessQueuedImportEvent::class)->everyMinute()->withoutOverlapping();
Schedule::command('backup:clean')->daily()->at('10:00');
Schedule::command('backup:run')->daily()->at('10:30');
