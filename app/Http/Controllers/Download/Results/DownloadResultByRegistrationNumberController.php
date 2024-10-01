<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Results;

use App\Enums\ImportEventMethod;
use App\Enums\ImportEventType;
use App\Http\Requests\Results\ResultRequest;
use App\Models\ImportEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;

final readonly class DownloadResultByRegistrationNumberController
{
    /** @throws \Exception */
    public function __invoke(ResultRequest $request): RedirectResponse
    {
        $user = $request->user();

        $event = ImportEvent::new($user, ImportEventType::RESULTS, ImportEventMethod::REGISTRATION_NUMBER,
            ['registration_number' => $request->string('registration_number')->value()]);

        Artisan::queue('portal-data:import', ['eventId' => $event->id]);

        return redirect()->back()->success('Results Import Started...');
    }
}
