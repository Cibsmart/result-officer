<?php

declare(strict_types=1);

namespace App\Http\Controllers\Imports;

use App\Models\ImportEvent;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Context;

final class ContinueImportEventController
{
    public function __invoke(ImportEvent $event): void
    {
        Context::add('event-type', $event->type->value);
        Artisan::queue('portal-data:process', ['eventId' => $event->id]);
    }
}
