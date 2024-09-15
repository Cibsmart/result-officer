<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Courses;

use App\Console\Commands\ImportCourses;
use App\Enums\ImportEventStatus;
use App\Enums\ImportEventType;
use App\Models\ImportEvent;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final readonly class DownloadCoursesController
{
    /** @throws \Exception */
    public function __invoke(Request $request): RedirectResponse
    {
        try {
            $event = new ImportEvent();

            $event->user_id = $request->user()->id;
            $event->type = ImportEventType::COURSES->value;
            $event->data = ['courses' => 'all'];
            $event->count = 0;
            $event->status = ImportEventStatus::NEW->value;
            $event->save();

            defer(fn () => (new ImportCourses())->handle());

            return back()->with(['events' => ImportEvent::all()])->success('Course Import Started');
        } catch (Exception $e) {
            return redirect()->back()->error($e->getMessage());
        }
    }
}
