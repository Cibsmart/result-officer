<?php

declare(strict_types=1);

namespace App\Http\Controllers\Reports;

use App\Data\Level\LevelListData;
use App\Data\Program\ProgramListData;
use App\Data\Semester\SemesterListData;
use App\Data\Session\SessionListData;
use App\ViewModels\Reports\CompositeFormPage;
use Inertia\Inertia;
use Inertia\Response;

final readonly class CompositeSheetController
{
    public function form(): Response
    {
        return Inertia::render('reports/composite/form/page', new CompositeFormPage(
            program: ProgramListData::new(),
            semester: SemesterListData::new(),
            session: SessionListData::new(),
            level: LevelListData::new(),
        ));
    }

    public function view(): Response
    {
        return Inertia::render('reports/composite/view/page');
    }
}
