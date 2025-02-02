<?php

declare(strict_types=1);

namespace App\Http\Controllers\Exports\Results;

use App\Exports\ResultsExport;
use App\Http\Requests\DepartmentSessionRequest;
use App\Models\Department;
use App\Models\Session;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class ExportResultsByDepartmentSessionController
{
    public function store(DepartmentSessionRequest $request): RedirectResponse
    {
        $department = $request->string('departmentName')->value();
        $session = $request->string('sessionName')->value();

        return redirect()->back()->success("Result export for {$department} {$session} session started...");
    }

    public function download(Department $department, Session $session): Response|BinaryFileResponse
    {
        $studentIds = $department->students()
            ->where('entry_session_id', $session->id)
            ->pluck('students.id')
            ->toArray();

        return ResultsExport::forStudents($studentIds)->download("{$department->slug}-{$session->slug}-results.xlsx");
    }
}
