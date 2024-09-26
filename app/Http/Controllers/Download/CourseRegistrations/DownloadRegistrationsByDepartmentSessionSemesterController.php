<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\CourseRegistrations;

use App\Enums\ImportEventMethod;
use App\Enums\ImportEventType;
use App\Http\Requests\Download\DownloadRegistrationsByDepartmentSessionSemesterRequest;
use App\Models\ImportEvent;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;

final readonly class DownloadRegistrationsByDepartmentSessionSemesterController
{
    public function __invoke(DownloadRegistrationsByDepartmentSessionSemesterRequest $request): RedirectResponse
    {
        $user = $request->user();

        assert($user instanceof User);

        $event = ImportEvent::new(
            user: $user,
            type: ImportEventType::REGISTRATIONS,
            method: ImportEventMethod::DEPARTMENT_SESSION_SEMESTER,
            data: [
                'department' => $request->string('departmentName')->value(),
                'online_department_id' => $request->integer('onlineDepartmentId'),
                'semester' => $request->string('semesterName')->value(),
                'session' => $request->string('sessionName')->value(),
            ],

        );

        Artisan::queue('portal-data:import', ['eventId' => $event->id]);

        return redirect()->back()->success('Course Registrations Import Started...');
    }
}
