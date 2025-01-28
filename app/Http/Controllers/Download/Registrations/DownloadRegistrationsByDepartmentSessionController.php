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

        $data = ['department' => $deptName, 'online_department_id' => $onlineId, 'session' => $session];

        $type = ImportEventType::REGISTRATIONS;
        $method = ImportEventMethod::DEPARTMENT_SESSION;

        if (ImportEvent::inQueue($type, $method, $data)) {
            $message = "Course Registration Download for {$deptName} {$session} session already queued";

            return redirect()->back()->error($message);
        }

        ImportEvent::new(user: $user, type: $type, method: $method, data: $data, status: ImportEventStatus::QUEUED);

        return redirect()->back()->success("Course Registration Download for {$deptName} {$session} session QUEUED");
    }
}
