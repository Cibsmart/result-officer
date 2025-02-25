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

final class RegistrationNumberListResultsExportController
{
    public function store(RegistrationNumberListResultsExportRequest $request): RedirectResponse
    {
        $count = $request->validated('registration_numbers')->count();

        return redirect()->back()->success("Result export for {$count} Registration Numbers started...");
    }

    public function download(Request $request): Response|BinaryFileResponse
    {
        $validated = $request->validate(['registration_numbers' => ['required', 'string']]);

        $registrationNumbersText = $validated['registration_numbers'];

        $registrationNumbers = Str::of($registrationNumbersText)
            ->replace("\n", ',')
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
