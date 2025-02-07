<?php

declare(strict_types=1);

namespace App\Http\Controllers\FinalResults;

use App\Actions\Import\Excel\ValidateHeadings;
use App\Enums\ExcelImportType;
use App\Imports\FinalResultsImport;
use App\Models\ExcelImportEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\HeadingRowImport;

final class ImportFinalResultController
{
    public function create(): Response
    {
        return Inertia::render('finalResults/import/page');
    }

    public function store(Request $request): RedirectResponse
    {
        $uploadedFile = $request->validate(['file' => ['required', 'file', 'mimes:xlsx']])['file'];

        $fileName = $uploadedFile->getClientOriginalName();

        $headings = (new HeadingRowImport())->toArray($uploadedFile)[0][0];

        [$passed, $validated, $missing] = (new ValidateHeadings())->execute($headings, ExcelImportType::FINAL_RESULTS);

        if (! $passed) {
            return redirect()->back()->error("Invalid File: The following headings are missing: {$missing}.");
        }

        $filePath = Storage::putFile('finalResults', $uploadedFile);
        assert(is_string($filePath));

        $event = ExcelImportEvent::new($request->user(), $filePath, $fileName);

        FinalResultsImport::new($event, $validated)->import($filePath);

        return redirect()->back()->success('Final results imported successfully.');
    }
}
