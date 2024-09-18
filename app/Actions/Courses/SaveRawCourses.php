<?php

declare(strict_types=1);

namespace App\Actions\Courses;

use App\Enums\ImportEventStatus;
use App\Models\ImportEvent;
use Illuminate\Support\Collection;

final class SaveRawCourses
{
    public function __construct(public SaveRawCourse $saveRawCourseAction)
    {
    }

    /** @param \Illuminate\Support\Collection<int, \App\Data\Download\PortalCourseData> $courses */
    public function execute(ImportEvent $event, Collection $courses): void
    {

        $event->updateStatus(ImportEventStatus::SAVING);

        foreach ($courses as $course) {
            $this->saveRawCourseAction->execute($event, $course);
        }

        $event->updateStatus(ImportEventStatus::SAVED);
    }
}
