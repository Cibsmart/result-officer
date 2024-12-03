<?php

declare(strict_types=1);

namespace App\Http\Controllers\Reports;

use App\Data\Cleared\ClearedStudentListData;
use App\Data\Department\DepartmentListData;
use App\Http\Requests\ClearedIndexRequest;
use App\Models\Department;
use App\ViewModels\Reports\ClearedIndexPageData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class DepartmentClearedController
{
    public function index(
        Request $request,
        ?Department $department = null,
        ?int $year = null,
    ): Response|RedirectResponse {
        $request->validate(['year' => 'nullable|int']);

        return Inertia::render('reports/cleared/index/page', new ClearedIndexPageData(
            departments: fn () => DepartmentListData::new(),
            students: fn () => $department !== null && $year !== null
                ? ClearedStudentListData::fromModel($department, $year)
                : null,
        ));
    }

    public function store(ClearedIndexRequest $request): RedirectResponse
    {
        $department = $request->input('department');
        $year = $request->input('year');

        return redirect()->route('department.cleared.index',
            ['department' => $department['id'], 'year' => $year['id']]);
    }
}
