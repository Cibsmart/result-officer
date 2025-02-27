<?php

declare(strict_types=1);

namespace App\Http\Controllers\Exports\Results;

use App\Data\Department\DepartmentListData;
use App\Data\Session\SessionListData;
use App\Models\User;
use App\ViewModels\Exports\ExportResultsPage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class ExportResultsPageController
{
    public function __invoke(Request $request): Response
    {
        $selectedIndex = $request->has('selectedIndex')
            ? $request->integer('selectedIndex')
            : 1;

        $user = $request->user();
        assert($user instanceof User);

        return Inertia::render('export/results/page', new ExportResultsPage(
            departments: DepartmentListData::forUser($user),
            sessions: SessionListData::new(),
            selectedIndex: $selectedIndex),
        );
    }
}
