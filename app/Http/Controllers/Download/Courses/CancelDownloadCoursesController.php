<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Courses;

use App\Enums\ImportEventStatus;
use App\Models\ImportEvent;

final class CancelDownloadCoursesController
{
    public function __invoke(ImportEvent $event): void
    {
        $event->updateStatus(ImportEventStatus::CANCELLED);
    }
}
