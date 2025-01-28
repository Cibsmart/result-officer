<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Departments;

use App\Enums\ImportEventMethod;
use App\Enums\ImportEventStatus;
use App\Enums\ImportEventType;
use App\Models\ImportEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final readonly class DownloadDepartmentsController
{
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = ['department' => 'all'];

        $type = ImportEventType::DEPARTMENTS;
        $method = ImportEventMethod::ALL;

        if (ImportEvent::inQueue($type, $method, $data)) {
            $message = 'Department Download is already in queue';

            return redirect()->back()->error($message);
        }

        ImportEvent::new(user: $user, type: $type, method: $method, data: $data, status: ImportEventStatus::QUEUED);

        return redirect()->back()->success('Department Download QUEUED');
    }
}
