<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Courses;

use App\Enums\ImportEventMethod;
use App\Enums\ImportEventStatus;
use App\Enums\ImportEventType;
use App\Models\ImportEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final readonly class DownloadCoursesController
{
    /** @throws \Exception */
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = ['course' => 'all'];

        $type = ImportEventType::COURSES;
        $method = ImportEventMethod::ALL;

        if (ImportEvent::inQueue($type, $method, $data)) {
            $message = 'Course Download is already in queue';

            return redirect()->back()->error($message);
        }

        ImportEvent::new(user: $user, type: $type, method: $method, data: $data, status: ImportEventStatus::QUEUED);

        return redirect()->back()->success('Course Download QUEUED');
    }
}
