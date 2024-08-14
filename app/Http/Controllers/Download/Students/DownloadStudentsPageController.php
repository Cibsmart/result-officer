<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Students;

use App\Data\Department\DepartmentListData;
use App\Data\Session\SessionListData;
use App\ViewModels\Downloads\DownloadStudentPage;
use Inertia\Inertia;
use Inertia\Response;

final readonly class DownloadStudentsPageController
{
    public function __invoke(): Response
    {
        return Inertia::render('download/students/page', new DownloadStudentPage(
            department: DepartmentListData::new(),
            session: SessionListData::new(),
        ));
    }
}
