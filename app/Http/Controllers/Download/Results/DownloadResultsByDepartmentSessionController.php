<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Results;

use App\Enums\ImportEventMethod;
use App\Enums\ImportEventStatus;
use App\Enums\ImportEventType;
use App\Http\Requests\Download\DownloadStudentsByDepartmentSessionRequest;
use App\Models\ImportEvent;
use Illuminate\Http\RedirectResponse;

final class DownloadResultsByDepartmentSessionController
{
    public function __invoke(DownloadStudentsByDepartmentSessionRequest $request): RedirectResponse
    {
        $user = $request->user();

        $deptName = $request->string('departmentName')->value();
        $onlineId = $request->integer('onlineDepartmentId');
        $session = $request->string('sessionName')->value();
        $data = ['department' => $deptName, 'online_department_id' => $onlineId, 'session' => $session];

        $type = ImportEventType::RESULTS;
        $method = ImportEventMethod::DEPARTMENT_SESSION;

        if (ImportEvent::inQueue($type, $method, $data)) {
            return redirect()->back()->error("Results Import for {$deptName} {$session} session is already in queue");
        }

        ImportEvent::new(user: $user, type: $type, method: $method, data: $data, status: ImportEventStatus::QUEUED);

        return redirect()->back()->success("Results Download for {$deptName} {$session} session QUEUED");
    }
}
