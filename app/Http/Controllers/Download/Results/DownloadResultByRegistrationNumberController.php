<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Results;

use App\Enums\ImportEventMethod;
use App\Enums\ImportEventStatus;
use App\Enums\ImportEventType;
use App\Http\Requests\ExistingRegistrationNumberRequest;
use App\Models\ImportEvent;
use Illuminate\Http\RedirectResponse;

final readonly class DownloadResultByRegistrationNumberController
{
    /** @throws \Exception */
    public function __invoke(ExistingRegistrationNumberRequest $request): RedirectResponse
    {
        $user = $request->user();

        $registrationNumber = $request->string('registration_number')->value();

        $data = ['registration_number' => $registrationNumber];

        $type = ImportEventType::RESULTS;
        $method = ImportEventMethod::REGISTRATION_NUMBER;

        if (ImportEvent::inQueue($type, $method, $data)) {
            $message = "Result Download for {$registrationNumber} is already in queue";

            return redirect()->back()->error($message);
        }

        ImportEvent::new(user: $user, type: $type, method: $method, data: $data, status: ImportEventStatus::QUEUED);

        return redirect()->back()
            ->success("Result Download for {$registrationNumber} QUEUED");
    }
}
