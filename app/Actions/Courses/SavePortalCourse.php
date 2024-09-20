<?php

declare(strict_types=1);

namespace App\Actions\Courses;

use App\Data\Download\PortalCourseData;
use App\Enums\RawDataStatus;
use App\Models\ImportEvent;
use App\Models\RawCourse;

final class SavePortalCourse
{
    public function execute(ImportEvent $event, PortalCourseData $data): void
    {
        $exists = RawCourse::query()
            ->where('code', $data->code)
            ->where('title', $data->title)
            ->where('status', RawDataStatus::PROCESSED)
            ->exists();

        if ($exists) {
            return;
        }

        RawCourse::createFromPortalCourseData($data, $event);
    }
}
