<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Results;

use App\Enums\ImportEventMethod;
use App\Enums\ImportEventType;
use App\Http\Requests\Download\DownloadRegistrationsByDepartmentSessionLevelRequest;
use App\Models\ImportEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;

final readonly class DownloadResultByDepartmentSessionLevelController
{
    /** @throws \Exception */
    public function __invoke(DownloadRegistrationsByDepartmentSessionLevelRequest $request): RedirectResponse
    {
        $user = $request->user();

        $event = ImportEvent::new(
            user: $user,
            type: ImportEventType::RESULTS,
            method: ImportEventMethod::DEPARTMENT_SESSION_LEVEL,
            data: [
                'department' => $request->string('departmentName')->value(),
                'level' => $request->integer('levelName'),
                'online_department_id' => $request->integer('onlineDepartmentId'),
                'session' => $request->string('sessionName')->value(),
            ],
        );

        Artisan::queue('portal-data:import', ['eventId' => $event->id]);

        return redirect()->back()->success('Results Import Started...');
    }
}
