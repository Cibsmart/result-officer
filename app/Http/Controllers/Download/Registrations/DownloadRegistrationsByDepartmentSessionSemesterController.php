<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Registrations;

use App\Enums\ImportEventMethod;
use App\Enums\ImportEventStatus;
use App\Enums\ImportEventType;
use App\Http\Requests\Download\DownloadRegistrationsByDepartmentSessionSemesterRequest;
use App\Models\ImportEvent;
use Illuminate\Http\RedirectResponse;

final readonly class DownloadRegistrationsByDepartmentSessionSemesterController
{
    public function __invoke(DownloadRegistrationsByDepartmentSessionSemesterRequest $request): RedirectResponse
    {
        $user = $request->user();

        $deptName = $request->string('departmentName')->value();
        $onlineId = $request->integer('onlineDepartmentId');
        $session = $request->string('sessionName')->value();
        $semester = $request->string('semesterName')->value();

        $data = [
            'department' => $deptName, 'online_department_id' => $onlineId,
            'semester' => $semester, 'session' => $session,
        ];

        $type = ImportEventType::REGISTRATIONS;
        $method = ImportEventMethod::DEPARTMENT_SESSION_SEMESTER;

        if (ImportEvent::inQueue($type, $method, $data)) {
            $message = "Course Registration Download for {$deptName} {$session} session {$semester} semester already queued";

            return redirect()->back()->error($message);
        }

        ImportEvent::new(user: $user, type: $type, method: $method, data: $data, status: ImportEventStatus::QUEUED);

        return redirect()->back()
            ->success("Course Registration Download for {$deptName} {$session} session {$semester} semester QUEUED");
    }
}
