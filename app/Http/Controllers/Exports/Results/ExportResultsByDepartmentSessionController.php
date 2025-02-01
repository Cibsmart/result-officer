<?php
declare(strict_types=1);

namespace App\Http\Controllers\Exports\Results;

use App\Exports\ResultsExport;
use App\Http\Requests\DepartmentSessionRequest;
use App\Http\Requests\Download\DownloadStudentsByDepartmentSessionRequest;
use App\Http\Requests\Results\ResultRequest;
use App\Models\Department;
use App\Models\Session;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportResultsByDepartmentSessionController
{
    public function store(DepartmentSessionRequest $request): RedirectResponse
    {
        $department = $request->string('departmentName')->value();
        $session = $request->string('sessionName')->value();

        return redirect()->back()->success("Result export for {$department} {$session} session started...");
    }

    public function download(Department $department, Session $session): BinaryFileResponse
    {
        $studentIds = $department->students()
            ->where('entry_session_id', $session->id)
            ->pluck('students.id')
            ->toArray();

        return new ResultsExport($studentIds)->download("{$department->slug}-{$session->slug}-results.xlsx");
    }
}
