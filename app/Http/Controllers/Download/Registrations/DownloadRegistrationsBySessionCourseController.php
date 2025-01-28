<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Registrations;

use App\Enums\ImportEventMethod;
use App\Enums\ImportEventStatus;
use App\Enums\ImportEventType;
use App\Http\Requests\Download\DownloadRegistrationsBySessionCourseRequest;
use App\Models\ImportEvent;
use Illuminate\Http\RedirectResponse;

final readonly class DownloadRegistrationsBySessionCourseController
{
    public function __invoke(DownloadRegistrationsBySessionCourseRequest $request): RedirectResponse
    {
        $user = $request->user();

        $course = $request->string('courseName')->value();
        $onlineId = $request->integer('onlineCourseId');
        $session = $request->string('sessionName')->value();

        $data = ['course' => $course, 'online_course_id' => $onlineId, 'session' => $session];

        $type = ImportEventType::REGISTRATIONS;
        $method = ImportEventMethod::SESSION_COURSE;

        if (ImportEvent::inQueue($type, $method, $data)) {
            $message = "Course Registration Download for {$course} {$session} session already queued";

            return redirect()->back()->error($message);
        }

        ImportEvent::new(user: $user, type: $type, method: $method, data: $data, status: ImportEventStatus::QUEUED);

        return redirect()->back()->success("Course Registration Download for {$course} {$session} session QUEUED");
    }
}
