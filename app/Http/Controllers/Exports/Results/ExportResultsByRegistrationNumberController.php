<?php

declare(strict_types=1);

namespace App\Http\Controllers\Exports\Results;

use App\Exports\ResultsExport;
use App\Http\Requests\ExistingRegistrationNumberRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class ExportResultsByRegistrationNumberController
{
    public function store(ExistingRegistrationNumberRequest $request): RedirectResponse
    {
        $registrationNumber = $request->string('registration_number')->value();

        return redirect()->back()->success("Result export for {$registrationNumber} started...");
    }

    public function download(ExistingRegistrationNumberRequest $request): Response|BinaryFileResponse
    {
        $student = $request->input('student');

        return ResultsExport::forStudents([$student->id])->download("{$student->slug}-results.xlsx");
    }
}
