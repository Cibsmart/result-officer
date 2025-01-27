<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Registrations;

use App\Enums\ImportEventMethod;
use App\Enums\ImportEventStatus;
use App\Enums\ImportEventType;
use App\Http\Requests\Download\DownloadStudentsByDepartmentSessionRequest;
use App\Models\ImportEvent;
use Illuminate\Http\RedirectResponse;

final class DownloadRegistrationsByDepartmentSessionController
{
    public function __invoke(DownloadStudentsByDepartmentSessionRequest $request): RedirectResponse
    {
        $user = $request->user();

        $session = $request->string('sessionName')->value();
        $onlineId = $request->integer('onlineDepartmentId');
        $deptName = $request->string('departmentName')->value();

        $importEventInQueue = ImportEvent::inSessionDepartmentQueue(
            ImportEventType::REGISTRATIONS,
            ImportEventMethod::DEPARTMENT_SESSION,
            $session,
            $onlineId,
        );

        if ($importEventInQueue) {
            $message = "Course Registrations Import for {$deptName} {$session} is already in queue";

            return redirect()->back()->error($message);
        }

        ImportEvent::new(
            user: $user,
            type: ImportEventType::REGISTRATIONS,
            method: ImportEventMethod::DEPARTMENT_SESSION,
            data: ['department' => $deptName, 'online_department_id' => $onlineId, 'session' => $session],
            status: ImportEventStatus::QUEUED,
        );

        return redirect()->back()->success("Course Registrations Import for {$deptName} {$session} QUEUED");
    }
}
