<?php

declare(strict_types=1);

namespace App\Http\Controllers\Exports\Results;

use App\Exports\ResultsExport;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ExportResultsByRegistrationNumberListController
{
    public function store(RegistrationNumberListResultsExportRequest $request): RedirectResponse
    {
        return redirect()->back()->success('Result export for Registration Number List started...');
    }

    public function download(Request $request): Response|BinaryFileResponse
    {
        $registrationNumbersText = $request->string('registration_numbers')->value();

        $registrationNumbers = Str::of($registrationNumbersText)
            ->replace(' ', '')
            ->explode(',')
            ->filter()
            ->unique();

        $uuid = Str::of(Str::ulid()->toString())->reverse()->limit(10, '')->reverse()->value();

        $studentIds = Student::query()
            ->whereIn('registration_number', $registrationNumbers)
            ->pluck('id');

        return ResultsExport::forStudents($studentIds->toArray())->download("students-{$uuid}-results.xlsx");
    }
}
