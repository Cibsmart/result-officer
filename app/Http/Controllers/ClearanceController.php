<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Clearing\ClearStudent;
use App\Enums\Months;
use App\Models\FinalStudent;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

final class ClearanceController
{
    public function store(
        Student $student,
        Request $request,
        ClearStudent $action,
    ): RedirectResponse {
        $validated = $request->validate([
            'exam_officer' => ['required', 'integer'],
            'month' => ['required', Rule::enum(Months::class)],
            'year' => ['required', 'integer', 'regex:/^\d{4}$/'],
        ]);
        $user = $request->user();
        assert($user instanceof User);

        $data = [
            'exam_officer_id' => $validated['exam_officer'],
            'month' => $validated['month'],
            'user_id' => $user->id,
            'year' => $validated['year'],
        ];

        try {
            DB::transaction(static function () use ($student, $data, $action): void {
                $finalStudent = FinalStudent::fromStudent($student, $data);
                $action->execute($student, $finalStudent);
            });
        } catch (Throwable $e) {
            report($e);

            return redirect()->back()->error(
                "Could not clear {$student->registration_number}. The record was left unchanged.",
            );
        }

        activity()
            ->causedBy($request->user())
            ->performedOn($student)
            ->log('cleared student');

        return to_route('graduand.index', $student->department())->success("{$student->registration_number} Cleared");
    }
}
