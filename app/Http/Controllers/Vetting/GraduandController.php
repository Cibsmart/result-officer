<?php

declare(strict_types=1);

namespace App\Http\Controllers\Vetting;

use App\Data\Department\DepartmentInfoData;
use App\Data\Graduands\PaginatedGraduandListData;
use App\Models\Department;
use App\ViewModels\Graduands\GraduandIndexPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class GraduandController
{
    public function index(?Department $department = null): Response
    {
        return Inertia::render('graduands/index/page', new GraduandIndexPage(
            department: fn () => $department ? DepartmentInfoData::for($department) : null,
            data: fn () => $department ? PaginatedGraduandListData::for($department)->paginated : null,
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(['department' => ['required', 'integer', 'exists:departments,id']]);

        $department = Department::query()->where('id', $validated['department'])->first();

        return redirect()->to(route('graduand.index', ['department' => $department]));
    }
}
