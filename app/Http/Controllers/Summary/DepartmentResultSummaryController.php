<?php

declare(strict_types=1);

namespace App\Http\Controllers\Summary;

use App\Data\Department\DepartmentListData;
use App\Data\Level\LevelListData;
use App\Data\Session\SessionListData;
use App\Data\Summary\DepartmentResultSummaryData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Summary\SummaryRequest;
use App\Models\Department;
use App\Models\Level;
use App\Models\Session;
use App\ViewModels\Summary\SummaryFormPage;
use App\ViewModels\Summary\SummaryViewPage;
use Illuminate\View\View;
use Inertia\Inertia;
use Inertia\Response;

final class DepartmentResultSummaryController extends Controller
{
    public function form(): Response
    {
        return Inertia::render('summary/form/page', new SummaryFormPage(
            department: DepartmentListData::new(),
            session: SessionListData::new(),
            level: LevelListData::new(),
        ));
    }

    public function view(SummaryRequest $request): Response
    {
        return Inertia::render('summary/view/page', new SummaryViewPage(
            department: DepartmentResultSummaryData::from(
                $request->input('department'),
                $request->input('session'),
                $request->input('level'),
            ),
        ));
    }

    public function print(
        Department $department,
        Session $session,
        Level $level,
    ): View {
        return view('pdfs.summary.view', [
            'summary' => DepartmentResultSummaryData::from($department, $session, $level),
        ]);
    }
}
