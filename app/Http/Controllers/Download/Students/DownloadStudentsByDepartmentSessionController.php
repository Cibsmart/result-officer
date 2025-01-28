<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Students;

use App\Enums\ImportEventMethod;
use App\Enums\ImportEventStatus;
use App\Enums\ImportEventType;
use App\Http\Requests\Download\DownloadStudentsByDepartmentSessionRequest;
use App\Models\ImportEvent;
use Illuminate\Http\RedirectResponse;

final readonly class DownloadStudentsByDepartmentSessionController
{
    public function __invoke(DownloadStudentsByDepartmentSessionRequest $request): RedirectResponse
    {
        $user = $request->user();

        $department = $request->string('departmentName')->value();
        $onlineId = $request->integer('onlineDepartmentId');
        $session = $request->string('sessionName')->value();

        $data = ['department' => $department, 'entry_session' => $session, 'online_department_id' => $onlineId];

        $type = ImportEventType::STUDENTS;
        $method = ImportEventMethod::DEPARTMENT_SESSION;

        if (ImportEvent::inQueue($type, $method, $data)) {
            $message = "Student Download for {$department} {$session} session is already in queue";

            return redirect()->back()->error($message);
        }

        ImportEvent::new(user: $user, type: $type, method: $method, data: $data, status: ImportEventStatus::QUEUED);

        return redirect()->back()->success("Students Download for {$department} {$session} session QUEUED");
    }
}
