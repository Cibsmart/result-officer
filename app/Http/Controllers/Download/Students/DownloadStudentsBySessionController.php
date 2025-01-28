<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Students;

use App\Enums\ImportEventMethod;
use App\Enums\ImportEventStatus;
use App\Enums\ImportEventType;
use App\Http\Requests\Download\DownloadStudentsBySessionRequest;
use App\Models\ImportEvent;
use Illuminate\Http\RedirectResponse;

final readonly class DownloadStudentsBySessionController
{
    public function __invoke(DownloadStudentsBySessionRequest $request): RedirectResponse
    {
        $user = $request->user();

        $session = $request->string('sessionName')->value();
        $data = ['entry_session' => $session];

        $type = ImportEventType::STUDENTS;
        $method = ImportEventMethod::SESSION;

        if (ImportEvent::inQueue($type, $method, $data)) {
            $message = "Student Download for {$session} session is already in queue";

            return redirect()->back()->error($message);
        }

        ImportEvent::new(user: $user, type: $type, method: $method, data: $data, status: ImportEventStatus::QUEUED);

        return redirect()->back()->success("Students Download for {$session} session QUEUED");
    }
}
