<?php

declare(strict_types=1);

namespace App\Console\Commands\Courses;

use App\Actions\Courses\SaveRawCourses;
use App\Enums\ImportEventStatus;
use App\Models\ImportEvent;
use App\Services\Api\CourseService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

final class ImportAndSaveCourses extends Command
{
    protected $signature = 'courses:import {eventId}';

    protected $description = 'Import Courses from the Portal and Save in the Database';

    public function handle(CourseService $service, SaveRawCourses $saveRawCoursesAction): void
    {
        $event = ImportEvent::findOrFail($this->argument('eventId'));

        $event->updateStatus(ImportEventStatus::DOWNLOADING);

        try {
            $courses = $service->getAllCourses();
        } catch (Exception $e) {
            $event->message = $e->getMessage();
            $event->updateStatus(ImportEventStatus::FAILED);

            return;
        }

        $event->download_count = $courses->count();
        $event->save();

        $saveRawCoursesAction->execute($event, $courses);

        Artisan::call('courses:process', ['eventId' => $event->id]);
    }
}
