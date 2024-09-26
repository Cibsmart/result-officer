<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\CourseRegistrations;

use App\Enums\ImportEventMethod;
use App\Enums\ImportEventType;
use App\Http\Requests\Download\DownloadRegistrationsBySessionCourseRequest;
use App\Models\ImportEvent;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;

final readonly class DownloadRegistrationsBySessionCourseController
{
    public function __invoke(DownloadRegistrationsBySessionCourseRequest $request): RedirectResponse
    {
        $user = $request->user();

        assert($user instanceof User);

        $event = ImportEvent::new($user, ImportEventType::REGISTRATIONS, ImportEventMethod::REGISTRATION_NUMBER,
            [
                'course' => $request->string('courseName')->value(),
                'online_course_id' => $request->integer('onlineCourseId'),
                'session' => $request->string('sessionName')->value(),
            ],
        );

        Artisan::queue('portal-data:import', ['eventId' => $event->id]);

        return redirect()->back()->success('Course Registrations Import Started...');
    }
}
