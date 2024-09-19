<?php

declare(strict_types=1);

namespace App\Http\Controllers\Imports;

use App\Models\ImportEvent;
use Illuminate\Support\Facades\Artisan;

final class ContinueImportEventController
{
    public function __invoke(ImportEvent $event): void
    {
        Artisan::queue("{$event->type}:process", ['eventId' => $event->id]);
    }
}
