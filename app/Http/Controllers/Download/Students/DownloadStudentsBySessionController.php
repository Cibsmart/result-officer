<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Students;

use App\Enums\ImportEventType;
use App\Http\Requests\Download\DownloadStudentsBySessionRequest;
use App\Models\ImportEvent;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;

final readonly class DownloadStudentsBySessionController
{
    public function __invoke(DownloadStudentsBySessionRequest $request): RedirectResponse
    {
        $user = $request->user();

        assert($user instanceof User);

        $event = ImportEvent::new($user, ImportEventType::STUDENTS,
            ['entry_session' => $request->string('sessionName')->value()]);

        Artisan::queue('portal-data:import', ['eventId' => $event->id]);

        return redirect()->back()->success('Students Import Started...');
    }
}
