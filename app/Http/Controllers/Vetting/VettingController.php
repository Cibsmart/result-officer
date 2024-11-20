<?php

declare(strict_types=1);

namespace App\Http\Controllers\Vetting;

use App\Data\Department\DepartmentListData;
use App\Data\Vetting\VettingListData;
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

final class VettingController
{
    public function index(?Department $department = null): Response
    {
        return Inertia::render('vetting/list/index/page', new VettingIndexPage(
            departments: DepartmentListData::new(),
            data: fn () => $department ? VettingListData::fromModel($department) : null,
        ));
    }

    public function create(Student $student, Request $request): RedirectResponse
    {
        $user = $request->user();
        assert($user instanceof User);

        $vettingEvent = VettingEvent::getOrCreateUsingStudent($student, $user);

        Artisan::queue('app:vet', ['vettingEventId' => $vettingEvent->id]);

        return redirect()->back()->success("Vetting Started for {$student->registration_number}");
    }
}
