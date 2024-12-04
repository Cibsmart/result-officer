<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Courses;

use App\Enums\ImportEventMethod;
use App\Enums\ImportEventType;
use App\Models\ImportEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

final readonly class DownloadCoursesController
{
    /** @throws \Exception */
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();

        $event = ImportEvent::new($user, ImportEventType::COURSES, ImportEventMethod::ALL, ['course' => 'all']);

        Artisan::queue('rp:import-portal-data', ['eventId' => $event->id]);

        return redirect()->back()->success('Course Import Started...');
    }
}
