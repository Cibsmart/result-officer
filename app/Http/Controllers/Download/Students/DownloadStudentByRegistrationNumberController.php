<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Students;

use App\Enums\ImportEventMethod;
use App\Enums\ImportEventStatus;
use App\Enums\ImportEventType;
use App\Http\Requests\Download\DownloadStudentByRegistrationRequest;
use App\Models\ImportEvent;
use Illuminate\Http\RedirectResponse;

final readonly class DownloadStudentByRegistrationNumberController
{
    public function __invoke(DownloadStudentByRegistrationRequest $request): RedirectResponse
    {
        $user = $request->user();
        $registrationNumber = $request->string('registration_number')->value();

        $data = ['registration_number' => $registrationNumber];

        $type = ImportEventType::STUDENTS;
        $method = ImportEventMethod::REGISTRATION_NUMBER;

        if (ImportEvent::inQueue($type, $method, $data)) {
            $message = "Student Download for {$registrationNumber} is already in queue";

            return redirect()->back()->error($message);
        }

        ImportEvent::new(user: $user, type: $type, method: $method, data: $data, status: ImportEventStatus::QUEUED);

        return redirect()->back()->success("Students Download for {$registrationNumber} QUEUED");
    }
}
