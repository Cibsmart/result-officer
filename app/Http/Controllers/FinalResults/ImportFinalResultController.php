<?php

declare(strict_types=1);

namespace App\Http\Controllers\FinalResults;

use App\Actions\Import\Excel\ValidateHeadings;
use App\Enums\ExcelImportType;
use App\Models\ExcelImportEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\HeadingRowImport;

final class ImportFinalResultController
{
    public function index(): Response
    {
        return Inertia::render('finalResults/import/page');
    }

    public function store(Request $request): RedirectResponse
    {
        $uploadedFile = $request->validate(['file' => ['required', 'file', 'mimes:xlsx']])['file'];

        $fileName = $uploadedFile->getClientOriginalName();

        $headings = (new HeadingRowImport())->toArray($uploadedFile)[0][0];

        $validation = (new ValidateHeadings())->execute($headings, ExcelImportType::FINAL_RESULTS);

        if (! $validation['passed']) {
            $message = "Invalid File: The following headings are missing: {$validation['missing']}.";

            return redirect()->back()->error($message);
        }

        $filePath = Storage::putFile('finalResults', $uploadedFile);
        assert(is_string($filePath));

        ExcelImportEvent::new($request->user(), $filePath, $fileName);

        return redirect()->back()->success('Final results excel file uploaded successfully.');
    }
}
