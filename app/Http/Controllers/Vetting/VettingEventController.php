<?php

declare(strict_types=1);

namespace App\Http\Controllers\Vetting;

use App\Enums\NotificationType;
use App\Models\Department;
use App\Models\Student;
use App\Models\User;
use App\Models\VettingEventGroup;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class VettingEventController
{
    public function index(): Response
    {
        return Inertia::render('vetting/index/page');
    }

    public function store(VettingStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = $request->user();
        assert($user instanceof User);

        $department = Department::getUsingId($validated['department']);

        $students = Student::query()->whereIn('registration_number', $validated['registration_numbers'])->get();

        $vettingGroup = VettingEventGroup::new($user, $department);

        $vettingGroup->addStudents($user, $students);

        $message = "Vetting of {$students->count()} students in {$department->name} department queued for processing.";

        return redirect()->back()->{NotificationType::SUCCESS->value}($message);
    }
}
