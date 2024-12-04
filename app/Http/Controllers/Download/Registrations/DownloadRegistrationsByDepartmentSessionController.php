<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Registrations;

use App\Enums\ImportEventMethod;
use App\Enums\ImportEventType;
use App\Http\Requests\Download\DownloadStudentsByDepartmentSessionRequest;
use App\Models\ImportEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;

final class DownloadRegistrationsByDepartmentSessionController
{
    public function __invoke(DownloadStudentsByDepartmentSessionRequest $request): RedirectResponse
    {
        $user = $request->user();

        $event = ImportEvent::new(
            user: $user,
            type: ImportEventType::REGISTRATIONS,
            method: ImportEventMethod::DEPARTMENT_SESSION,
            data: [
                'department' => $request->string('departmentName')->value(),
                'online_department_id' => $request->integer('onlineDepartmentId'),
                'session' => $request->string('sessionName')->value(),
            ],
        );

        Artisan::queue('rp:import-portal-data', ['eventId' => $event->id]);

        return redirect()->back()->success('Course Registrations Import Started...');
    }
}
