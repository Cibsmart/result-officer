<?php

declare(strict_types=1);

namespace App\Http\Controllers\Summary;

use App\Data\Department\DepartmentListData;
use App\Data\Level\LevelListData;
use App\Data\Session\SessionListData;
use App\Http\Controllers\Controller;
use App\ViewModels\Summary\SummaryViewPage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class DepartmentResultSummaryController extends Controller
{
    public function form(): Response
    {
        return Inertia::render('summary/form/page', new SummaryViewPage(
            department: DepartmentListData::new(),
            session: SessionListData::new(),
            level: LevelListData::new(),
        ));
    }

    public function view(Request $request): Response
    {
        dd($request->all());

        return Inertia::render('summary/view/page');
    }
}
