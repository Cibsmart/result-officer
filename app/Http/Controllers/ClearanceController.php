<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Clearing\ClearStudent;
use App\Enums\Months;
use App\Models\FinalStudent;
use App\Models\Student;
use App\Models\User;
use Exception;
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
        dd($validated);

        $user = $request->user();
        assert($user instanceof User);

        $data = [
            'exam_officer_id' => $validated['exam_officer'],
            'month' => $validated['month'],
            'user_id' => $user->id,
            'year' => $validated['year'],
        ];

        try {
            DB::beginTransaction();
            $finalStudent = FinalStudent::fromStudent($student, $data);
            $action->execute($student, $finalStudent);
            DB::commit();
        } catch (Exception $e) {
            return redirect()->back()->error($e->getMessage());
        } catch (Throwable $e) {
            return redirect()->back()->error($e->getMessage());
        }

        activity()
            ->causedBy($request->user())
            ->performedOn($student)
            ->log('cleared student');

        return to_route('vetting.index', $student->department())->success("{$student->registration_number} Cleared");
    }
}
