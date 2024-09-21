<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Students;

use App\Enums\ImportEventMethod;
use App\Enums\ImportEventType;
use App\Http\Requests\Download\DownloadStudentByRegistrationRequest;
use App\Models\ImportEvent;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;

final readonly class DownloadStudentByRegistrationNumberController
{
    public function __invoke(DownloadStudentByRegistrationRequest $request): RedirectResponse
    {
        $user = $request->user();

        assert($user instanceof User);

        $event = ImportEvent::new($user, ImportEventType::STUDENTS, ImportEventMethod::REGISTRATION_NUMBER,
            ['registration_number' => $request->string('registration_number')->value()]);

        Artisan::queue('portal-data:import', ['eventId' => $event->id]);

        return redirect()->back()->success('Students Import Started...');
    }
}
