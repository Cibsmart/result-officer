<?php

declare(strict_types=1);

namespace App\Actions\Courses;

use App\Enums\ImportEventStatus;
use App\Enums\RawDataStatus;
use App\Models\ImportEvent;
use Exception;

final class ProcessImportedRawCourses
{
    public function __construct(public SaveImportedRawCourse $saveCourseAction)
    {
    }

    public function execute(ImportEvent $event): void
    {
        $event->updateStatus(ImportEventStatus::PROCESSING);

        $rawCourses = $event->courses()->where('status', RawDataStatus::PENDING)->get();

        foreach ($rawCourses as $rawCourse) {
            try {
                $this->saveCourseAction->execute($rawCourse);
            } catch (Exception $e) {
                $rawCourse->setMessage($e->getMessage());

                $rawCourse->updateStatus(RawDataStatus::FAILED);
            }
        }

        $event->updateStatus(ImportEventStatus::COMPLETED);
    }
}
