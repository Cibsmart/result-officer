<?php

declare(strict_types=1);

namespace App\Http\Controllers\Vetting;

use App\Data\Department\DepartmentInfoData;
use App\Data\Department\DepartmentListData;
use App\Data\Vetting\PaginatedVettingListData;
use App\Data\Vetting\VettingStepListData;
use App\Models\Department;
use App\Models\Student;
use App\Models\User;
use App\Models\VettingEvent;
use App\ViewModels\Clearance\ClearanceFormPage;
use App\ViewModels\Vetting\VettingIndexPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;
use Inertia\Response;

use function assert;
use function Illuminate\Support\defer;

final class VettingController
{
    public function index(Request $request, ?Department $department = null): Response
    {
        $student = Student::query()->where('slug', $request->query('student'))->first();

        $user = $request->user();
        assert($user instanceof User);

        return Inertia::render('vetting/list/index/page', new VettingIndexPage(
            departments: fn () => DepartmentListData::forUser($user),
            clearance: fn () => ClearanceFormPage::new(),
            steps: fn () => $student ? VettingStepListData::from($student) : null,
            department: fn () => $department ? DepartmentInfoData::for($department) : null,
            data: fn () => $department ? PaginatedVettingListData::for($department)->paginated : null,
        ));
    }

    public function create(Student $student, Request $request): RedirectResponse
    {
        $user = $request->user();
        assert($user instanceof User);

        $vettingEvent = VettingEvent::getOrCreateUsingStudent($student, $user);

        defer(fn () => Artisan::call('rp:vet', ['vettingEventId' => $vettingEvent->id]));

        return redirect()->back()->success("Vetting Started for {$student->registration_number}");
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'department' => ['required', 'array'],
            'department.id' => ['required', 'integer', 'exists:departments,id'],
        ]);

        $department = Department::query()
            ->where('id', $validated['department']['id'])
            ->first();

        return redirect()->to(route('vetting.index', ['department' => $department]));
    }
}
