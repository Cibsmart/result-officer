<?php

declare(strict_types=1);

namespace App\Http\Controllers\Vetting;

use App\Data\Department\DepartmentInfoData;
use App\Data\Vetting\PaginatedVettingListData;
use App\Data\Vetting\VettingStepListData;
use App\Models\Department;
use App\Models\Student;
use App\ViewModels\Vetting\GraduandIndexPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class GraduandController
{
    public function index(Request $request, ?Department $department = null): Response
    {
        $student = Student::query()->where('slug', $request->query('student'))->first();

        return Inertia::render('graduands/index/page', new GraduandIndexPage(
            steps: fn () => $student ? VettingStepListData::from($student) : null,
            department: fn () => $department ? DepartmentInfoData::for($department) : null,
            data: fn () => $department ? PaginatedVettingListData::for($department)->paginated : null,
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(['department' => ['required', 'integer', 'exists:departments,id']]);

        $department = Department::query()->where('id', $validated['department'])->first();

        return redirect()->to(route('graduand.index', ['department' => $department]));
    }
}
