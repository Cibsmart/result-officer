<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Registrations;

use App\Enums\ImportEventMethod;
use App\Enums\ImportEventType;
use App\Http\Requests\Results\ResultRequest;
use App\Models\ImportEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;

final readonly class DownloadRegistrationsByRegistrationNumberController
{
    public function __invoke(ResultRequest $request): RedirectResponse
    {
        $user = $request->user();

        $event = ImportEvent::new($user, ImportEventType::REGISTRATIONS, ImportEventMethod::REGISTRATION_NUMBER,
            ['registration_number' => $request->string('registration_number')->value()]);

        Artisan::queue('rp:import-portal-data', ['eventId' => $event->id]);

        return redirect()->back()->success('Course Registrations Import Started...');
    }
}
