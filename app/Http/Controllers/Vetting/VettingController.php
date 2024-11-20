<?php

declare(strict_types=1);

namespace App\Http\Controllers\Vetting;

use App\Data\Department\DepartmentListData;
use App\Data\Vetting\VettingListData;
use App\Models\Department;
use App\ViewModels\Vetting\VettingFormPage;
use App\ViewModels\Vetting\VettingViewPage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

use function assert;

final class VettingController
{
    public function form(): Response
    {
        return Inertia::render('vetting/list/form/page', new VettingFormPage(
            departments: DepartmentListData::new(),
        ));
    }

    public function view(Request $request): Response
    {
        $department = Department::query()->findOrFail($request->input('department.id'));
        assert($department instanceof Department);

        return Inertia::render('vetting/list/view/page', new VettingViewPage(
            data: VettingListData::fromModel($department),
        ));
    }
}
