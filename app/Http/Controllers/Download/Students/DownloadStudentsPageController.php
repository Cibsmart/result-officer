<?php

declare(strict_types=1);

namespace App\Http\Controllers\Download\Students;

use App\Data\Department\DepartmentListData;
use App\Data\Session\SessionListData;
use App\Http\Controllers\Controller;
use App\ViewModels\Downloads\DownloadStudentPage;
use Inertia\Inertia;
use Inertia\Response;

final class DownloadStudentsPageController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('students/download/page', new DownloadStudentPage(
            department: DepartmentListData::new(),
            session: SessionListData::new(),
        ));
    }
}
