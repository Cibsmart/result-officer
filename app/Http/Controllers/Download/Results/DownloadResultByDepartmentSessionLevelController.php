<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Results;

use App\Enums\ImportEventMethod;
use App\Enums\ImportEventStatus;
use App\Enums\ImportEventType;
use App\Http\Requests\Download\DownloadRegistrationsByDepartmentSessionLevelRequest;
use App\Models\ImportEvent;
use Illuminate\Http\RedirectResponse;

final readonly class DownloadResultByDepartmentSessionLevelController
{
    /** @throws \Exception */
    public function __invoke(DownloadRegistrationsByDepartmentSessionLevelRequest $request): RedirectResponse
    {
        $user = $request->user();

        $department = $request->string('departmentName')->value();
        $onlineId = $request->integer('onlineDepartmentId');
        $session = $request->string('sessionName')->value();
        $level = $request->integer('levelName');
        $data = [
            'department' => $department, 'level' => $level, 'online_department_id' => $onlineId, 'session' => $session,
        ];

        $type = ImportEventType::RESULTS;
        $method = ImportEventMethod::DEPARTMENT_SESSION_LEVEL;

        if (ImportEvent::inQueue($type, $method, $data)) {
            $message = "Result Download for {$department} {$session} session {$level} level is already in queue";

            return redirect()->back()->error($message);
        }

        ImportEvent::new(user: $user, type: $type, method: $method, data: $data, status: ImportEventStatus::QUEUED);

        return redirect()->back()
            ->success("Result Download for {$department} {$session} session {$level} level QUEUED");
    }
}
