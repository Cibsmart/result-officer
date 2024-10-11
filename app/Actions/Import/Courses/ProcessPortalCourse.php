<?php

declare(strict_types=1);

namespace App\Actions\Import\Courses;

use App\Enums\RawDataStatus;
use App\Models\Course;
use App\Models\RawCourse;

final class ProcessPortalCourse
{
    public function execute(RawCourse $rawCourse): void
    {
        $exists = Course::query()
            ->where('code', $rawCourse->code)
            ->where('title', $rawCourse->title)
            ->exists();

        if ($exists) {
            $rawCourse->updateStatus(RawDataStatus::DUPLICATE);

            return;
        }

        Course::createFromRawCourse($rawCourse);

        $rawCourse->updateStatus(RawDataStatus::PROCESSED);
    }
}
