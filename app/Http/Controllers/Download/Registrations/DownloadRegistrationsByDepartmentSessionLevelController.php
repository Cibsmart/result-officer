<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Registrations;

use App\Enums\ImportEventMethod;
use App\Enums\ImportEventStatus;
use App\Enums\ImportEventType;
use App\Http\Requests\Download\DownloadRegistrationsByDepartmentSessionLevelRequest;
use App\Models\ImportEvent;
use Illuminate\Http\RedirectResponse;

final readonly class DownloadRegistrationsByDepartmentSessionLevelController
{
    public function __invoke(DownloadRegistrationsByDepartmentSessionLevelRequest $request): RedirectResponse
    {
        $user = $request->user();

        $deptName = $request->string('departmentName')->value();
        $onlineId = $request->integer('onlineDepartmentId');
        $session = $request->string('sessionName')->value();
        $level = $request->integer('levelName');

        $data = [
            'department' => $deptName, 'level' => $level, 'online_department_id' => $onlineId, 'session' => $session,
        ];

        $type = ImportEventType::REGISTRATIONS;
        $method = ImportEventMethod::DEPARTMENT_SESSION_LEVEL;

        if (ImportEvent::inQueue($type, $method, $data)) {
            $message = "Course Registration Download for {$deptName} {$session} session {$level} Level already queued";

            return redirect()->back()->error($message);
        }

        ImportEvent::new(user: $user, type: $type, method: $method, data: $data, status: ImportEventStatus::QUEUED);

        return redirect()->back()
            ->success("Course Registration Download for {$deptName} {$session} session {$level} Level QUEUED");
    }
}
