<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Departments;

use App\Enums\ImportEventType;
use App\Models\ImportEvent;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

final readonly class DownloadDepartmentsController
{
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();

        assert($user instanceof User);

        $event = ImportEvent::new($user, ImportEventType::DEPARTMENTS, ['department' => 'all']);

        defer(fn () => Artisan::queue('departments:import', ['eventId' => $event->id]));

        return redirect()->back()->success('Department Import Started...');
    }
}
