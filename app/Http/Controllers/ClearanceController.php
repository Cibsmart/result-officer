<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Clearing\ClearStudent;
use App\Models\Student;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class ClearanceController
{
    public function __construct(public ClearStudent $action)
    {
    }

    public function store(Student $student, Request $request): RedirectResponse
    {
        activity()
            ->causedBy($request->user())
            ->performedOn($student)
            ->log('cleared student');

        try {
            $this->action->execute($student);
        } catch (Exception $e) {
            return redirect()->back()->error($e->getMessage());
        }

        return to_route('vetting.index', $student->department())->success("{$student->registration_number} Cleared");
    }
}
