<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Registrations;

use App\Enums\ImportEventMethod;
use App\Enums\ImportEventStatus;
use App\Enums\ImportEventType;
use App\Http\Requests\Results\ResultRequest;
use App\Models\ImportEvent;
use Illuminate\Http\RedirectResponse;

final readonly class DownloadRegistrationsByRegistrationNumberController
{
    public function __invoke(ResultRequest $request): RedirectResponse
    {
        $user = $request->user();

        $registrationNumber = $request->string('registration_number')->value();

        $data = ['registration_number' => $registrationNumber];

        $type = ImportEventType::REGISTRATIONS;
        $method = ImportEventMethod::REGISTRATION_NUMBER;

        if (ImportEvent::inQueue($type, $method, $data)) {
            $message = "Course Registration Download for {$registrationNumber} already queued";

            return redirect()->back()->error($message);
        }

        ImportEvent::new(user: $user, type: $type, method: $method, data: $data, status: ImportEventStatus::QUEUED);

        return redirect()->back()->success("Course Registration Download for {$registrationNumber} QUEUED");
    }
}
