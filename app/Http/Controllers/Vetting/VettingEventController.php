<?php

declare(strict_types=1);

namespace App\Http\Controllers\Vetting;

use App\Data\Vetting\PaginatedVettingEventGroupListData;
use App\Data\Vetting\VettingEventGroupDetailData;
use App\Enums\NotificationType;
use App\Enums\VettingEventStatus;
use App\Models\Department;
use App\Models\Student;
use App\Models\User;
use App\Models\VettingEventGroup;
use App\ViewModels\Vetting\VettingIndexPage;
use App\ViewModels\Vetting\VettingShowPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class VettingEventController
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        assert($user instanceof User);

        return Inertia::render('vetting/index/page', new VettingIndexPage(
            paginated: PaginatedVettingEventGroupListData::forUser($user)->paginated,
        ));
    }

    public function store(VettingStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = $request->user();
        assert($user instanceof User);

        $department = Department::getUsingId($validated['department']);

        $students = Student::query()
            ->whereIn('registration_number', $validated['registration_numbers'])
            ->orderBy('registration_number')
            ->get();

        $vettingGroup = VettingEventGroup::new($user, $department, $validated['title']);

        $vettingGroup->addStudents($user, $students);

        $message = "Vetting of {$students->count()} students in {$department->name} department queued for processing.";

        return redirect()->back()->{NotificationType::SUCCESS->value}($message);
    }

    public function show(VettingEventGroup $vettingEvent): Response
    {
        return Inertia::render('vetting/show/page', new VettingShowPage(
            data: Inertia::defer(fn () => VettingEventGroupDetailData::for($vettingEvent)),
        ));
    }

    public function destroy(VettingEventGroup $vettingEvent): RedirectResponse
    {
        if ($vettingEvent->status !== VettingEventStatus::QUEUED) {
            return redirect()->back()->error('Only queued events can be deleted.');
        }

        $vettingEvent->delete();

        return redirect()->back()->success('Vetting Deleted successfully.');
    }
}
