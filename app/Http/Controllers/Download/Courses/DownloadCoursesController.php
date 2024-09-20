<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Courses;

use App\Enums\ImportEventType;
use App\Models\ImportEvent;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Context;

final readonly class DownloadCoursesController
{
    /** @throws \Exception */
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();

        assert($user instanceof User);

        $event = ImportEvent::new($user, ImportEventType::COURSES, ['course' => 'all']);

        Context::add('import-event', ImportEventType::COURSES->value);
        defer(fn () => Artisan::queue('portal-data:import', ['eventId' => $event->id]));

        return redirect()->back()->success('Course Import Started...');
    }
}
