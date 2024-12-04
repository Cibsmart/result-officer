<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Students;

use App\Enums\ImportEventMethod;
use App\Enums\ImportEventType;
use App\Http\Requests\Download\DownloadStudentsByDepartmentSessionRequest;
use App\Models\ImportEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;

final readonly class DownloadStudentsByDepartmentSessionController
{
    public function __invoke(DownloadStudentsByDepartmentSessionRequest $request): RedirectResponse
    {
        $user = $request->user();

        $event = ImportEvent::new($user, ImportEventType::STUDENTS, ImportEventMethod::DEPARTMENT_SESSION,
            [
                'department' => $request->string('departmentName')->value(),
                'entry_session' => $request->string('sessionName')->value(),
                'online_department_id' => $request->integer('onlineDepartmentId'),
            ]);

        Artisan::queue('rp:import-portal-data', ['eventId' => $event->id]);

        return redirect()->back()->success('Students Import Started...');
    }
}
