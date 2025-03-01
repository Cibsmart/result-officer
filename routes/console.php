<?php

declare(strict_types=1);

use App\Console\Commands\ProcessQueuedImportEvent;
use App\Console\Commands\ProcessRawExcelUploads;
use App\Console\Commands\UploadPendingExcelImports;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command(ProcessQueuedImportEvent::class)->everyMinute()->runInBackground();
Schedule::command(UploadPendingExcelImports::class)
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();
Schedule::command(ProcessRawExcelUploads::class)
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('backup:clean')->daily()->at('10:00');
Schedule::command('backup:run')->daily()->at('10:30');
