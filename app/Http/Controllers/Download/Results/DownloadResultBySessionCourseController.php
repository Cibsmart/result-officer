<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Results;

use App\Enums\ImportEventMethod;
use App\Enums\ImportEventStatus;
use App\Enums\ImportEventType;
use App\Http\Requests\Download\DownloadRegistrationsBySessionCourseRequest;
use App\Models\ImportEvent;
use Illuminate\Http\RedirectResponse;

final readonly class DownloadResultBySessionCourseController
{
    public function __invoke(DownloadRegistrationsBySessionCourseRequest $request): RedirectResponse
    {
        $user = $request->user();

        $session = $request->string('sessionName')->value();
        $course = $request->string('courseName')->value();
        $onlineId = $request->integer('onlineCourseId');
        $data = ['course' => $course, 'online_course_id' => $onlineId, 'session' => $session];

        $type = ImportEventType::RESULTS;
        $method = ImportEventMethod::SESSION_COURSE;

        if (ImportEvent::inQueue($type, $method, $data)) {
            return redirect()->back()->error("Results Import for {$course} {$session} session is already in queue");
        }

        ImportEvent::new(user: $user, type: $type, method: $method, data: $data, status: ImportEventStatus::QUEUED);

        return redirect()->back()->success("Results Download for {$course} {$session} session QUEUED");
    }
}
