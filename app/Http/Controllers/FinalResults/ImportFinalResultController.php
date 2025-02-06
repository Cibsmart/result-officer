<?php

declare(strict_types=1);

namespace App\Http\Controllers\FinalResults;

use App\Imports\FinalResultsImport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

final class ImportFinalResultController
{
    public function create(): Response
    {
        return Inertia::render('finalResults/import/page');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(['file' => ['required', 'file', 'mimes:xlsx']]);

        $filePath = Storage::putFile('finalResults', $validated['file']);
        assert(is_string($filePath));

        (new FinalResultsImport())->import($filePath);

        return redirect()->back()->success('Final results imported successfully.');
    }
}
