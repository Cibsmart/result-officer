<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Results;

use App\Enums\ImportEventMethod;
use App\Enums\ImportEventStatus;
use App\Enums\ImportEventType;
use App\Http\Requests\Download\DownloadRegistrationsByDepartmentSessionSemesterRequest;
use App\Models\ImportEvent;
use Illuminate\Http\RedirectResponse;

final readonly class DownloadResultByDepartmentSessionSemesterController
{
    /** @throws \Exception */
    public function __invoke(DownloadRegistrationsByDepartmentSessionSemesterRequest $request): RedirectResponse
    {
        $user = $request->user();

        $department = $request->string('departmentName')->value();
        $semester = $request->string('semesterName')->value();
        $session = $request->string('sessionName')->value();
        $onlineId = $request->integer('onlineDepartmentId');
        $data = [
            'department' => $department, 'online_department_id' => $onlineId, 'semester' => $semester,
            'session' => $session,
        ];

        $type = ImportEventType::RESULTS;
        $method = ImportEventMethod::DEPARTMENT_SESSION_SEMESTER;

        if (ImportEvent::inQueue($type, $method, $data)) {
            $message = "Result Download for {$department} {$session} session {$semester} semester is already in queue";

            return redirect()->back()->error($message);
        }

        ImportEvent::new(user: $user, type: $type, method: $method, data: $data, status: ImportEventStatus::QUEUED);

        return redirect()->back()
            ->success("Result Download for {$department} {$session} session {$semester} semester QUEUED");
    }
}
