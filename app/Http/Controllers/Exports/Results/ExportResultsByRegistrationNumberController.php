<?php
declare(strict_types=1);

namespace App\Http\Controllers\Exports\Results;

use App\Exports\ResultsExport;
use App\Http\Requests\Results\ResultRequest;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportResultsByRegistrationNumberController
{
    public function store(ResultRequest $request): RedirectResponse
    {
        $registrationNumber = $request->string('registration_number')->value();

        return redirect()->back()->success("Result export for {$registrationNumber} started...");
    }

    public function download(Request $request): BinaryFileResponse
    {
        $registrationNumber = $request->string('registration_number')->value();

        $student = Student::where('registration_number', $registrationNumber)->firstOrFail();

        return new ResultsExport([$student->id])->download("{$student->slug}-results.xlsx");
    }
}
