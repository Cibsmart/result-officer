<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Clearing\ClearStudent;
use App\Models\FinalStudent;
use App\Models\Student;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

final class ClearanceController
{
    public function store(Student $student, Request $request, ClearStudent $action): RedirectResponse
    {
        $request->validate([
            'year' => ['required', 'array'],
            'year.name' => ['required', 'integer'],
            'month' => ['required', 'array'],
            'month.name' => ['required', 'string'],
            'exam_officer' => ['required', 'array'],
            'exam_officer.id' => ['required', 'integer'],
        ]);

        $data = [
            'user_id' => $request->user()->id,
            'year' => $request->year['name'],
            'month' => $request->month['name'],
            'exam_officer_id' => $request->exam_officer['id'],
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
