<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Students;

use App\Data\Department\DepartmentListData;
use App\Data\Import\ImportEventData;
use App\Data\Import\PendingImportEventData;
use App\Data\Session\SessionListData;
use App\Enums\ImportEventType;
use App\Models\User;
use App\ViewModels\Downloads\DownloadStudentPage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final readonly class DownloadStudentsPageController
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        assert($user instanceof User);

        return Inertia::render('download/students/page', new DownloadStudentPage(
            department: DepartmentListData::new(),
            session: SessionListData::new(),
            events: fn () => ImportEventData::new($user, ImportEventType::STUDENTS),
            pending: fn () => PendingImportEventData::new($user, ImportEventType::STUDENTS)),
        );
    }
}
