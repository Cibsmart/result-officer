<?php

declare(strict_types=1);

namespace App\Http\Controllers\Vetting;

use App\Data\Department\DepartmentInfoData;
use App\Data\Vetting\PaginatedVettingListData;
use App\Data\Vetting\VettingStepListData;
use App\Models\Department;
use App\Models\Student;
use App\Models\User;
use App\Models\VettingEvent;
use App\ViewModels\Vetting\VettingIndexPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;
use Inertia\Response;

use function assert;
use function Illuminate\Support\defer;

final class GraduandController
{
    public function index(Request $request, ?Department $department = null): Response
    {
        $student = Student::query()->where('slug', $request->query('student'))->first();

        return Inertia::render('graduands/index/page', new VettingIndexPage(
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
        $validated = $request->validate(['department' => ['required', 'integer', 'exists:departments,id']]);

        $department = Department::query()->where('id', $validated['department'])->first();

        return redirect()->to(route('graduand.index', ['department' => $department]));
    }
}
